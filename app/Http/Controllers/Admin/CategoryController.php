<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // On utilise un nom de clé plus précis
        $cacheKey = 'categories.root';

        $categories = Cache::remember($cacheKey, now()->addDays(7), function () {
            return Category::whereDoesntHave('parent')->get();
        });

        // On applique la Resource APRÈS la récupération du cache
        return response()->json([
            'categories' => $categories
        ], 200);
    }

    public function all()
    {
        $cacheKey = 'categories.all';

        // 1. On cache UNIQUEMENT les données brutes (Eloquent)
        $categories = Cache::remember($cacheKey, now()->addDays(7), function () {
            return Category::with(['genders', 'parent']) // Ajout de 'parent' pour le Resource
                ->withCount('products') // CRUCIAL : Évite le N+1 du Resource
                ->orderBy('created_at', 'desc') // Ou orderBy('category_name', 'asc') pour un tri alphabétique
                ->get();
        });

        // 2. On applique la Resource APRÈS la récupération du cache
        return response()->json([
            'categories' => CategoryResource::collection($categories)
        ], 200);
    }

    public function getCategory($id)
    {
        $category = Cache::remember('category.' . $id, now()->addDays(7), function () use ($id) {
            return CategoryResource::make(Category::findOrFail($id));
        });
        return response()->json(['category' => $category], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $category = Category::findOrFail($id);

            // 1. Sauvegarder l'ancienne URL pour invalider l'ancien cache
            $oldUrl = $category->category_url;

            // 2. Mettre à jour les données de base
            $category->category_name = $request->category_name;
            $category->parent_id = intval($request->parent_id ?? 0);

            // Astuce : Ajouter l'ID au slug pour éviter les conflits de slugs en doublon
            $category->category_url = Str::slug($request->category_name) . '-' . $category->id;

            // 3. Gérer l'image de profil
            if ($request->hasFile('category_profile')) {
                // Supprimer l'ancienne image si elle existe (optionnel mais recommandé)
                if ($category->category_profile) {
                    \Storage::disk('public')->delete($category->category_profile);
                }
                $category->category_profile = $request->file('category_profile')->store('categories/profile', 'public');
            }

            $category->save();

            // 4. Mettre à jour la relation avec le genre
            $category->genders()->sync([intval($request->gender_id)]);

            DB::commit();

            // 🚨 5. INVALIDATION DU CACHE (CRITIQUE)
            // On vide l'ancien cache (au cas où l'URL a changé)
            Cache::forget('category.url.' . $oldUrl);
            // On vide le nouveau cache pour qu'il se régénère avec les nouvelles données
            Cache::forget('category.url.' . $category->category_url);
            // On vide aussi le cache global de l'arbre des catégories (vu précédemment)
            Cache::forget('categories.root');

            // 6. Retour propre (pas $request->all())
            return response()->json([
                'success' => true,
                'message' => 'Catégorie mise à jour avec succès',
                'data' => new CategoryResource($category)
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la catégorie',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $createdCategories = [];

            // Tableau de correspondance propre pour éviter les bugs de variables non initialisées
            $genderMap = [
                '1' => 'homme',
                '2' => 'femme',
                '3' => 'enfant'
            ];

            if ($request->parent_id == null) {
                // --- CAS 1 : Catégorie Racine ---
                $category = new Category();
                $category->category_name = $request->category_name;
                $category->category_url = Str::slug($request->category_name) . '-' . time(); // Garantit l'unicité
                $category->parent_id = null;

                if ($request->hasFile('category_profile')) {
                    $category->category_profile = $request->file('category_profile')->store('categories/profile', 'public');
                }

                $category->save();

                if ($request->gender_id) {
                    $category->genders()->attach((int) $request->gender_id);
                }

                $createdCategories[] = $category;

            } else {
                // --- CAS 2 : Sous-catégories par Genre ---
                // S'assurer que gender_id est un tableau pour la boucle
                $genderIds = is_array($request->gender_id) ? $request->gender_id : [$request->gender_id];

                foreach ($genderIds as $genderId) {
                    $genderName = $genderMap[(string) $genderId] ?? 'inconnu';

                    $category = new Category();
                    $category->category_name = $request->category_name . ' ' . ucfirst($genderName);
                    $category->category_url = Str::slug($request->category_name . ' ' . $genderName) . '-' . time();
                    $category->parent_id = (int) $request->parent_id;

                    if ($request->hasFile('category_profile')) {
                        $category->category_profile = $request->file('category_profile')->store('categories/profile', 'public');
                    }

                    $category->save();
                    $category->genders()->attach((int) $genderId);

                    $createdCategories[] = $category;
                }
            }

            DB::commit();

            // 🚨 CRITIQUE : Invalidation du cache après création réussie
            Cache::forget('categories.all');
            Cache::forget('categories.root');


            return response()->json([
                'success' => true,
                'message' => "Catégorie(s) créée(s) avec succès",
                'data' => $createdCategories
            ], 201); // 201 Crea

        } catch (\Exception $e) {
            DB::rollBack();

            // Nettoyage des fichiers uploadés en cas d'échec (optionnel mais recommandé)
            // ...

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     * 
     * This method safely deletes a category by checking dependencies first.
     * For cascade deletion, use forceDestroy method instead.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);

            // 1. Vérifier s'il y a des sous-catégories (exists() est plus rapide que count() > 0)
            if ($category->children()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une catégorie contenant des sous-catégories. Veuillez les supprimer d\'abord.'
                ], 400);
            }

            // 2. Vérifier s'il y a des produits associés
            if ($category->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer une catégorie contenant des produits. Veuillez les déplacer ou les supprimer d\'abord.'
                ], 400);
            }

            // 3. 🚨 Nettoyage : Supprimer l'image du serveur si elle existe
            if ($category->category_profile) {
                Storage::disk('public')->delete($category->category_profile);
            }

            // 4. Sauvegarder l'URL avant suppression pour invalider le cache spécifique
            $oldUrl = $category->category_url;
            $oldId = $category->id;

            // 5. Suppression des relations et de la catégorie
            $category->genders()->detach();
            $category->delete();

            // 🚨 CRITIQUE : Invalidation du cache après suppression
            Cache::forget('categories.all');
            Cache::forget('categories.root');
            Cache::forget('category.url.' . $oldUrl); // Invalide l'ancienne page de cette catégorie
            Cache::forget('category.id.' . $oldId);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie supprimée avec succès'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Catégorie introuvable'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force delete a category and all its subcategories (cascade deletion).
     * Use with caution as this will delete all related data.
     */
    public function forceDestroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);

            // Supprimer récursivement toutes les sous-catégories
            $this->deleteCategoryRecursively($category);

            return response()->json([
                'success' => true,
                'message' => 'Category and all subcategories deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to recursively delete categories and their relationships
     */
    private function deleteCategoryRecursively(Category $category)
    {
        // Supprimer d'abord les sous-catégories
        foreach ($category->children as $child) {
            $this->deleteCategoryRecursively($child);
        }

        // Supprimer les relations
        $category->genders()->detach();
        $category->products()->detach();
        $category->shops()->detach();

        // Supprimer la catégorie
        $category->delete();
    }
}