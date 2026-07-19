<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
;

class ProductListController extends Controller
{
    public function index()
    {
        $cacheKey = 'home.products.featured';

        // 🚨 Le mélange aléatoire n'est calculé que TOUTES LES 2 HEURES.
        // Le reste du temps, c'est instantané (0 requête SQL).
        $products = Cache::remember($cacheKey, now()->addHours(2), function () {
            return Product::where('status', 1)
                ->where('is_trashed', 0)
                ->where('isRejet', 0)
                //->with(['categories', 'shop:id,shop_name']) // 🚨 Évite le N+1 du Resource
                ->inRandomOrder()
                ->take(5)
                ->get();
        });

        return ProductResource::collection($products);
    }

    public function adsProducts($id)
    {
        $cacheKey = "products.ads.subscribe.{$id}";

        $products = Cache::remember($cacheKey, now()->addHours(2), function () use ($id) {
            return Product::where('subscribe_id', $id)
                ->where('status', 1)
                ->where('is_trashed', 0)
                ->where('isRejet', 0)
                ->inRandomOrder()
                ->get();
        });

        return ProductResource::collection($products);
    }
    public function allProducts(Request $request)
    {
        // 1. CLÉ DE CACHE INTELLIGENTE (MD5 des paramètres triés)
        $queryParams = $request->except(['page']);
        ksort($queryParams);
        $paramsHash = md5(json_encode($queryParams));
        $currentPage = $request->get('page', 1);

        $cacheKey = "products.all.{$paramsHash}.page.{$currentPage}";

        // 2. REQUÊTE DE BASE AVEC EAGER LOADING MASSIF
        $query = Product::with([
            'categories',
            'variations.color',
            'variations.attributesVariation.attributeValue',
            'variations.wholesalePrices',
            'wholesalePrices',
            'shop:id,shop_name' // Ajoute les relations dont ton ProductResource a besoin
        ])
            ->where('status', 1)
            ->where('is_trashed', 0)
            ->where('isRejet', 0)
            ->latest('created_at'); // 🚨 CRUCIAL : Remplace inRandomOrder() par latest() pour la pagination

        // 3. FILTRES (Ta logique originale, conservée car elle est correcte)
        if ($request->has('min_price') || $request->has('max_price')) {
            $minPrice = $request->input('min_price') ? floatval($request->input('min_price')) : null;
            $maxPrice = $request->input('max_price') ? floatval($request->input('max_price')) : null;

            $query->where(function (Builder $subQuery) use ($minPrice, $maxPrice) {
                $subQuery->where(function ($q) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null)
                        $q->whereRaw('CAST(product_price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    if ($maxPrice !== null)
                        $q->whereRaw('CAST(product_price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                })->orWhereHas('variations', function ($q) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null)
                        $q->whereRaw('CAST(price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    if ($maxPrice !== null)
                        $q->whereRaw('CAST(price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                })->orWhereHas('variations.attributesVariation', function ($q) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null)
                        $q->whereRaw('CAST(price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    if ($maxPrice !== null)
                        $q->whereRaw('CAST(price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                });
            });
        }

        if ($request->has('categories')) {
            $categoryIds = explode(',', $request->input('categories'));
            $query->whereHas('categories', function (Builder $q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        if ($request->has('colors')) {
            $colorNames = explode(',', $request->input('colors'));
            $query->whereHas('variations', function (Builder $q) use ($colorNames) {
                $q->whereHas('color', function (Builder $cq) use ($colorNames) {
                    $cq->whereIn('value', $colorNames);
                });
            });
        }

        if ($request->has('attributes')) {
            $attributesToFilter = $request->input('attributes');
            if (!is_array($attributesToFilter) && is_string($attributesToFilter)) {
                $attributesToFilter = [$attributesToFilter => []];
            }
            foreach ($attributesToFilter as $attributeId => $valueIdsString) {
                $valueIds = explode(',', $valueIdsString);
                $query->whereHas('variations.attributesVariation', function (Builder $q) use ($attributeId, $valueIds) {
                    $q->whereHas('attributeValue', function (Builder $aq) use ($valueIds) {
                        $aq->whereIn('id', $valueIds);
                    });
                });
            }
        }

        if ($request->has('gender')) {
            $query->where('product_gender', $request->input('gender'));
        }

        if ($request->has('seller_mode') && $request->input('seller_mode') == true) {
            $query->where('is_wholesale', 1);
            if ($request->has('bulk_price_range')) {
                list($minBulkPrice, $maxBulkPrice) = explode('-', $request->input('bulk_price_range'));
                $minBulkPrice = floatval($minBulkPrice);
                $maxBulkPrice = floatval($maxBulkPrice);

                $query->where(function (Builder $q) use ($minBulkPrice, $maxBulkPrice) {
                    $q->whereHas('wholesalePrices', function ($wq) use ($minBulkPrice, $maxBulkPrice) {
                        $wq->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) >= ?', [$minBulkPrice]);
                        $wq->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) <= ?', [$maxBulkPrice]);
                    })->orWhereHas('variations', function ($vq) use ($minBulkPrice, $maxBulkPrice) {
                        $vq->whereHas('wholesalePrices', function ($wq) use ($minBulkPrice, $maxBulkPrice) {
                            $wq->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) >= ?', [$minBulkPrice]);
                            $wq->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) <= ?', [$maxBulkPrice]);
                        });
                    })->orWhereHas('variations.attributesVariation', function ($aq) use ($minBulkPrice, $maxBulkPrice) {
                        $aq->whereHas('wholesalePrices', function ($wq) use ($minBulkPrice, $maxBulkPrice) {
                            $wq->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) >= ?', [$minBulkPrice]);
                            $wq->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) <= ?', [$maxBulkPrice]);
                        });
                    });
                });
            }
        }

        // 4. EXÉCUTION AVEC CACHE (15 minutes pour les recherches filtrées)
        $products = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($query) {
            return $query->paginate(20);
        });

        return ProductResource::collection($products);
    }
}
