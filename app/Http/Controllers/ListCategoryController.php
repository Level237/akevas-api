<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\CategoryResource;

class ListCategoryController extends Controller
{
    // 🛠️ HELPER : Évite la répétition de la logique de genre
    private function resolveGenderIds($genderId)
    {
        return $genderId == 4 ? [1, 2, 3] : [(int) $genderId];
    }

    public function getSubCategoriesByParentId($arrayIds, $id)
    {
        $array = trim($arrayIds, '[]');
        $items = array_map('intval', explode(',', $array));
        $items = array_filter($items); // Sécurité

        if (empty($items)) {
            return response()->json(['categories' => []], 200);
        }

        $finalIds = $this->resolveGenderIds($id);
        $cacheKey = "subcategories.parent." . md5($arrayIds) . ".gender.{$id}";

        $categories = Cache::remember($cacheKey, now()->addHours(12), function () use ($items, $finalIds) {
            return Category::whereIn('parent_id', $items)
                ->whereHas('genders', function ($query) use ($finalIds) {
                    $query->whereIn('genders.id', $finalIds);
                })
                ->with('genders') // 🚨 Évite le N+1 si le Resource en a besoin
                ->get();
        });

        return response()->json(['categories' => CategoryResource::collection($categories)], 200);
    }

    public function showCategoryByGender($id)
    {
        $finalIds = $this->resolveGenderIds($id);
        $cacheKey = "categories.root.gender.{$id}";

        $categories = Cache::remember($cacheKey, now()->addHours(12), function () use ($finalIds) {
            return Category::whereDoesntHave('parent')
                ->whereHas('genders', function ($query) use ($finalIds) {
                    $query->whereIn('genders.id', $finalIds);
                })
                ->with('genders')
                ->get();
        });

        return response()->json(['categories' => CategoryResource::collection($categories)], 200);
    }

    public function getCategoryWithParentIdNull(Request $request)
    {
        if ($request->has('gender') && $request->gender) {
            $genderId = $request->gender;
            $finalIds = $this->resolveGenderIds($genderId);
            $cacheKey = "root_categories_gender_{$genderId}";

            $rootCategories = Cache::remember($cacheKey, now()->addHours(12), function () use ($finalIds) {
                return Category::whereDoesntHave('parent')
                    ->whereHas('genders', function ($query) use ($finalIds) {
                        $query->whereIn('genders.id', $finalIds);
                    })
                    ->with('genders')
                    ->take(8)
                    ->get();
            });
        } else {
            $cacheKey = "root_categories_all";
            $rootCategories = Cache::remember($cacheKey, now()->addHours(12), function () {
                return Category::whereDoesntHave('parent')
                    ->with('genders')
                    ->take(8)
                    ->get();
            });
        }

        return CategoryResource::collection($rootCategories);
    }

    // 🚨 CORRECTION MAJEURE : Suppression de la boucle N+1 et de la récursion
    public function getCategoriesByGender($parentCategoryId, Request $request)
    {
        // 1. Charger le parent, ses enfants, et les genres des enfants EN UNE SEULE REQUÊTE
        $cacheKey = "category.tree.{$parentCategoryId}";

        $parent = Cache::remember($cacheKey, now()->addHours(12), function () use ($parentCategoryId) {
            return Category::with(['children.genders', 'children.children'])->find($parentCategoryId);
        });

        if (!$parent) {
            return response()->json(['message' => 'Catégorie parente non trouvée'], 404);
        }

        $children = $parent->children;

        // 2. Si un filtre de genre est demandé, on filtre la collection EN MÉMOIRE (PHP est ultra-rapide pour ça)
        if ($request->has('gender') && $request->gender) {
            $finalIds = $this->resolveGenderIds((int) $request->gender);

            $filtered = $children->filter(function ($child) use ($finalIds) {
                // On utilise la relation déjà chargée, PAS de nouvelle requête DB !
                return $child->genders->whereIn('id', $finalIds)->isNotEmpty();
            })->values();

            return response()->json(['categories' => CategoryResource::collection($filtered)], 200);
        }

        // 3. Groupement par genre EN MÉMOIRE (Zéro requête SQL supplémentaire)
        $categoriesByGender = [];

        foreach ($children as $child) {
            $genderNames = $child->genders->isNotEmpty()
                ? $child->genders->pluck('gender_name')->toArray()
                : ['sans_genre'];

            foreach ($genderNames as $genderName) {
                if (!isset($categoriesByGender[$genderName])) {
                    $categoriesByGender[$genderName] = [];
                }
                $categoriesByGender[$genderName][] = $child;

                // Gestion des petits-enfants (ancienne récursion remplacée par une lecture mémoire)
                if ($genderName === 'sans_genre' && $child->children->isNotEmpty()) {
                    foreach ($child->children as $grandChild) {
                        $categoriesByGender['sans_genre'][] = $grandChild;
                    }
                }
            }
        }

        // Nettoyer les doublons éventuels
        foreach ($categoriesByGender as $gender => $cats) {
            $categoriesByGender[$gender] = collect($cats)->unique('id')->values();
        }

        return response()->json($categoriesByGender);
    }

    public function getCategoriesWithAttributes()
    {
        $cacheKey = "categories.attributes.map";

        $data = Cache::remember($cacheKey, now()->addHours(24), function () {
            return DB::table('categories')
                ->join('category_atributes', 'categories.id', '=', 'category_atributes.category_id')
                ->join('attributes', 'category_atributes.attribute_id', '=', 'attributes.id')
                ->select(
                    'categories.id as category_id',
                    'categories.category_name as category_name',
                    'attributes.id as attribute_id',
                    'attributes.attributes_name as attribute_name'
                )
                ->get();
        });

        return response()->json($data);
    }

    // 🚨 CORRECTION MAJEURE : Suppression de la boucle N+1 dans les groupes
    public function getAttributeValueByAttributeId($attributeId)
    {
        $cacheKey = "attribute.values.grouped.{$attributeId}";

        $result = Cache::remember($cacheKey, now()->addHours(24), function () use ($attributeId) {
            // 1. Trouver le lien catégorie-attribut (si nécessaire, sinon on peut optimiser plus loin)
            $categoryAttributeLink = DB::table('category_atributes')
                ->where('attribute_id', $attributeId)
                ->first();

            if (!$categoryAttributeLink) {
                return [];
            }

            // 2. Récupérer TOUS les groupes en 1 seule requête
            $groups = DB::table('category_atribute_group_links')
                ->where('category_attribute_id', $categoryAttributeLink->id)
                ->join('attribute_value_groups', 'category_atribute_group_links.attribute_value_group_id', '=', 'attribute_value_groups.id')
                ->select('attribute_value_groups.id', 'attribute_value_groups.label')
                ->get();

            if ($groups->isEmpty()) {
                return [];
            }

            $groupIds = $groups->pluck('id');

            // 3. Récupérer TOUTES les valeurs de ces groupes en 1 SEULE requête (au lieu de N requêtes dans une boucle)
            $allValues = DB::table('attribute_values')
                ->whereIn('attribute_value_group_id', $groupIds)
                ->where('attribute_id', $attributeId)
                ->select('id', 'value', 'label', 'attribute_value_group_id')
                ->get()
                ->groupBy('attribute_value_group_id'); // On groupe en mémoire PHP

            // 4. Assembler le résultat en mémoire
            $result = [];
            foreach ($groups as $group) {
                $result[] = [
                    'group_id' => $group->id,
                    'group_label' => $group->label,
                    'values' => $allValues->get($group->id, collect())->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'value' => $item->value,
                            'label' => $item->label
                        ];
                    })->values(),
                ];
            }

            return $result;
        });

        return response()->json($result);
    }
}