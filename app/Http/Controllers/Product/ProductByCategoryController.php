<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
class ProductByCategoryController extends Controller
{
    public function index($categoryUrl, Request $request)
    {
        // 1. GÉNÉRATION D'UNE CLÉ DE CACHE UNIQUE ET STABLE
        // On prend tous les paramètres SAUF 'page', on les trie, et on crée un hash MD5
        $queryParams = $request->except(['page']);
        ksort($queryParams); // Garantit que ?color=red&size=M = ?size=M&color=red

        $paramsHash = md5(json_encode($queryParams));
        $currentPage = $request->get('page', 1);

        // Clé finale ex: products.cat.chaussures.a1b2c3d4.page.1
        $cacheKey = "products.cat.{$categoryUrl}.{$paramsHash}.page.{$currentPage}";

        // 2. CONSTRUCTION DE LA REQUÊTE (Identique à ta logique, mais avec Eager Loading massif)
        $query = Product::with([
            'categories',
            // 🚨 CRUCIAL : Charger TOUTES les relations utilisées dans whereHas ET dans ton ProductResource
            // pour éviter des centaines de requêtes N+1 après le cache
            'variations.color',
            'variations.attributesVariation.attributeValue',
            'variations.wholesalePrices',
            'wholesalePrices',
            'shop:id,shop_name' // Ajoute les relations de ton Resource ici
        ])
            ->whereHas('categories', function ($query) use ($categoryUrl) {
                $query->where('categories.category_url', $categoryUrl);
            })
            ->where('status', 1)
            ->where('is_trashed', 0)
            ->where('isRejet', 0);

        // --- FILTRE PRIX ---
        if ($request->has('min_price') || $request->has('max_price')) {
            $minPrice = $request->input('min_price') ? floatval($request->input('min_price')) : null;
            $maxPrice = $request->input('max_price') ? floatval($request->input('max_price')) : null;

            $query->where(function (Builder $subQuery) use ($minPrice, $maxPrice) {
                $subQuery->where(function ($simpleProductQuery) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null)
                        $simpleProductQuery->whereRaw('CAST(product_price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    if ($maxPrice !== null)
                        $simpleProductQuery->whereRaw('CAST(product_price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                })->orWhereHas('variations', function (Builder $variationQuery) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null)
                        $variationQuery->whereRaw('CAST(price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    if ($maxPrice !== null)
                        $variationQuery->whereRaw('CAST(price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                })->orWhereHas('variations.attributesVariation', function (Builder $attributeQuery) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null)
                        $attributeQuery->whereRaw('CAST(price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    if ($maxPrice !== null)
                        $attributeQuery->whereRaw('CAST(price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                });
            });
        }

        // --- FILTRE COULEURS ---
        if ($request->has('colors')) {
            $colorNames = explode(',', $request->input('colors'));
            $query->whereHas('variations', function (Builder $variationQuery) use ($colorNames) {
                $variationQuery->whereHas('color', function (Builder $colorQuery) use ($colorNames) {
                    $colorQuery->whereIn('value', $colorNames);
                });
            });
        }

        // --- FILTRE ATTRIBUTS ---
        if ($request->has('attributes')) {
            $attributesToFilter = $request->input('attributes');
            if (!is_array($attributesToFilter) && is_string($attributesToFilter)) {
                $attributesToFilter = [$attributesToFilter => []];
            }

            foreach ($attributesToFilter as $attributeId => $valueIdsString) {
                $valueIds = explode(',', $valueIdsString);
                $query->whereHas('variations.attributesVariation', function (Builder $attributeQuery) use ($attributeId, $valueIds) {
                    $attributeQuery->whereHas('attributeValue', function (Builder $attributeValueQuery) use ($valueIds) {
                        $attributeValueQuery->whereIn('id', $valueIds);
                    });
                });
            }
        }

        // --- FILTRE GENRE ---
        if ($request->has('gender')) {
            $query->where('product_gender', $request->input('gender'));
        }

        // --- FILTRE VENTE EN GROS (SELLER MODE) ---
        if ($request->has('seller_mode') && $request->input('seller_mode') == true) {
            $query->where('is_wholesale', 1);

            if ($request->has('bulk_price_range')) {
                list($minBulkPrice, $maxBulkPrice) = explode('-', $request->input('bulk_price_range'));
                $minBulkPrice = floatval($minBulkPrice);
                $maxBulkPrice = floatval($maxBulkPrice);

                $query->where(function (Builder $bulkPriceQuery) use ($minBulkPrice, $maxBulkPrice) {
                    $bulkPriceQuery->whereHas('wholesalePrices', function (Builder $wholesaleQuery) use ($minBulkPrice, $maxBulkPrice) {
                        $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) >= ?', [$minBulkPrice]);
                        $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) <= ?', [$maxBulkPrice]);
                    })->orWhereHas('variations', function (Builder $variationQuery) use ($minBulkPrice, $maxBulkPrice) {
                        $variationQuery->whereHas('wholesalePrices', function (Builder $wholesaleQuery) use ($minBulkPrice, $maxBulkPrice) {
                            $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) >= ?', [$minBulkPrice]);
                            $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) <= ?', [$maxBulkPrice]);
                        });
                    })->orWhereHas('variations.attributesVariation', function (Builder $attributeQuery) use ($minBulkPrice, $maxBulkPrice) {
                        $attributeQuery->whereHas('wholesalePrices', function (Builder $wholesaleQuery) use ($minBulkPrice, $maxBulkPrice) {
                            $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) >= ?', [$minBulkPrice]);
                            $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) <= ?', [$maxBulkPrice]);
                        });
                    });
                });
            }
        }

        // 3. EXÉCUTION AVEC CACHE (10 minutes est idéal pour du e-commerce filtré)
        $products = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($query) {
            return $query->paginate(6);
        });

        return ProductResource::collection($products);
    }
}
