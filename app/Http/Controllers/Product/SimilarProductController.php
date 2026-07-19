<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\SimpleProductResource;
use Illuminate\Support\Facades\Cache;

class SimilarProductController extends Controller
{
    public function getSimilarProducts($id)
    {
        $cacheKey = "products.similar.{$id}";

        // 1. Cache de 1 heure (les produits similaires ne changent pas souvent)
        $similarProducts = Cache::remember($cacheKey, now()->addHour(), function () use ($id) {

            // 2. Récupération sécurisée des catégories de référence
            $product = Product::find($id);
            if (!$product) {
                return collect(); // Retourne une collection vide si le produit n'existe pas
            }

            $referenceCategories = $product->categories()->pluck('categories.id');

            if ($referenceCategories->isEmpty()) {
                return collect();
            }

            // 3. Recherche des produits similaires AVEC LIMITES
            return Product::whereHas('categories', function ($query) use ($referenceCategories) {
                $query->whereIn('categories.id', $referenceCategories);
            })
                ->where('id', '!=', $id)
                ->where('status', 1)       //  Filtrer les produits actifs
                ->where('is_trashed', 0)    // 🚨 Filtrer les non-supprimés
                ->where('isRejet', 0)       // 🚨 Filtrer les non-rejetés
                ->inRandomOrder()           // Mélange pour varier l'affichage
                ->take(4)                   // 🚨 CRITIQUE : Limite à 4 produits pour éviter le crash mémoire !
                ->get();
        });

        return SimpleProductResource::collection($similarProducts);
    }
}
