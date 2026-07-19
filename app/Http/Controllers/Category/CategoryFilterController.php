<?php

namespace App\Http\Controllers\Category;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Cache;

class CategoryFilterController extends Controller
{
    public function filter($arrayId, Request $request)
    {
        $array = trim($arrayId, "[]");
        $items = array_map('intval', explode(',', $array));
        $items = array_filter($items);
        $items = array_unique($items);
        sort($items);

        if (empty($items)) {
            return ProductResource::collection(collect([]));
        }

        $page = $request->get('page', 1);
        $cacheKey = "filter.products.{$page}." . implode('_', $items);

        $products = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($items) {
            return Product::with('categories')
                ->whereHas('categories', function ($query) use ($items) {
                    $query->whereIn('categories.id', $items);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(6);
        });

        return ProductResource::collection($products);
    }

    public function getCategoryBySubCategory($arraySubCategoryId)
    {
        // 1. Nettoyage robuste et sécurisation des IDs
        $array = trim($arraySubCategoryId, "[]");
        $items = array_map('intval', explode(',', $array)); // Conversion en entier (sécurité)
        $items = array_filter($items); // Supprime les valeurs vides ou 0
        $items = array_unique($items); // Évite les doublons

        if (empty($items)) {
            return response()->json([]);
        }

        // 2. Récupération de l'arbre des catégories DEPUIS LE CACHE
        // On ne charge que les colonnes nécessaires (id, parent_id, etc.) pour économiser la mémoire
        $allCategories = Cache::remember('categories.tree', now()->addHours(1), function () {
            // Adapte 'category_name' et 'category_url' selon les vrais noms de tes colonnes
            return Category::select('id', 'parent_id', 'category_name', 'category_url')
                ->get()
                ->keyBy('id'); // Transforme la collection en dictionnaire [id => Category]
        });

        $rootCategories = collect();

        // 3. Traversée de l'arbre EN MÉMOIRE (Zéro requête SQL supplémentaire !)
        foreach ($items as $subCategoryId) {
            if (!isset($allCategories[$subCategoryId])) {
                continue; // Ignore les IDs invalides
            }

            $current = $allCategories[$subCategoryId];

            // On remonte l'arbre tant qu'il y a un parent_id ET que ce parent existe dans notre dictionnaire
            while ($current->parent_id !== null && isset($allCategories[$current->parent_id])) {
                $current = $allCategories[$current->parent_id];
            }

            $rootCategories->push($current);
        }

        // 4. Retourne les catégories racines uniques, réindexées
        return response()->json($rootCategories->unique('id')->values());
    }
}
