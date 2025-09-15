<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;


class ProductByCategoryController extends Controller
{
    public function index($categoryUrl,Request $request){
        $query=Product::with('categories')->whereHas('categories',function($query) use ($categoryUrl){
            $query->where('categories.category_url',$categoryUrl);
        })->where('status', 1)
        ->where('is_trashed', 0);


        if ($request->has('min_price') || $request->has('max_price')) {
            $minPrice = $request->input('min_price') ? floatval($request->input('min_price')) : null;
            $maxPrice = $request->input('max_price') ? floatval($request->input('max_price')) : null;
    
            $query->where(function (Builder $subQuery) use ($minPrice, $maxPrice) {
    
                // Filtre pour les produits simples (prix dans la table 'products')
                $subQuery->where(function ($simpleProductQuery) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null) {
                        $simpleProductQuery->whereRaw('CAST(product_price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    }
                    if ($maxPrice !== null) {
                        $simpleProductQuery->whereRaw('CAST(product_price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                    }
                });
    
                // Filtre pour les produits variés (couleur uniquement, prix dans la table 'product_variations')
                $subQuery->orWhereHas('variations', function (Builder $variationQuery) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null) {
                        $variationQuery->whereRaw('CAST(price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    }
                    if ($maxPrice !== null) {
                        $variationQuery->whereRaw('CAST(price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                    }
                });
                
                // Filtre pour les produits variés (couleur + attributs, prix dans 'variation_attributes')
                $subQuery->orWhereHas('variations.attributesVariation', function (Builder $attributeQuery) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null) {
                        $attributeQuery->whereRaw('CAST(price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    }
                    if ($maxPrice !== null) {
                        $attributeQuery->whereRaw('CAST(price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                    }
                });
            });
        }
    
        
    
        if ($request->has('colors')) {
            $colorsString = $request->input('colors');
            $colorNames = explode(',', $colorsString);
    
            $query->whereHas('variations', function (Builder $variationQuery) use ($colorNames) {
                $variationQuery->whereHas('color', function (Builder $colorQuery) use ($colorNames) {
                    $colorQuery->whereIn('value', $colorNames);
                });
            });
        }
    
        if ($request->has('attributes')) {
            $attributesToFilter = $request->input('attributes');
    
            // Check if the input is a string and handle it as a single attribute
            if (!is_array($attributesToFilter) && is_string($attributesToFilter)) {
                // Assuming a single attribute ID is passed, e.g., ?attributes=5
                $attributesToFilter = [$attributesToFilter => []];
            }
    
            foreach ($attributesToFilter as $attributeId => $valueIdsString) {
                $valueIds = explode(',', $valueIdsString);
                
                $query->whereHas('variations.attributesVariation', function (Builder $attributeQuery) use ($attributeId, $valueIds) {
                    $attributeQuery->whereHas('attributeValue', function (Builder $attributeValueQuery) use ($attributeId, $valueIds) {
                        $attributeValueQuery
                                            ->whereIn('id', $valueIds);
                    });
                });
            }
        }
    
        if ($request->has('gender')) {
            $genderId = $request->input('gender');
            $query->where('product_gender', $genderId);
        }
    
        if ($request->has('seller_mode') && $request->input('seller_mode')==true) {
            $query->where('is_wholesale',1);
    
            if ($request->has('bulk_price_range')) {
                list($minBulkPrice, $maxBulkPrice) = explode('-', $request->input('bulk_price_range'));
                
                $minBulkPrice = floatval($minBulkPrice);
                $maxBulkPrice = floatval($maxBulkPrice);
                // On regroupe toutes les conditions de prix de gros
                $query->where(function(Builder $bulkPriceQuery) use ($minBulkPrice, $maxBulkPrice) {
                    
                    // Cas 1: Prix de gros pour les produits simples (sans variations)
                    $bulkPriceQuery->whereHas('wholesalePrices', function (Builder $wholesaleQuery) use ($minBulkPrice, $maxBulkPrice) {
                        $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) >= ?', [$minBulkPrice]);
                        $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) <= ?', [$maxBulkPrice]);
                    });
                    
                    // Cas 2: Prix de gros pour les produits variés (couleur uniquement)
                    $bulkPriceQuery->orWhereHas('variations', function (Builder $variationQuery) use ($minBulkPrice, $maxBulkPrice) {
                        $variationQuery->whereHas('wholesalePrices', function (Builder $wholesaleQuery) use ($minBulkPrice, $maxBulkPrice) {
                            $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) >= ?', [$minBulkPrice]);
                            $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) <= ?', [$maxBulkPrice]);
                        });
                    });
    
                    // Cas 3: Prix de gros pour les produits variés (couleur + attribut)
                    $bulkPriceQuery->orWhereHas('variations.attributesVariation', function (Builder $attributeQuery) use ($minBulkPrice, $maxBulkPrice) {
                        $attributeQuery->whereHas('wholesalePrices', function (Builder $wholesaleQuery) use ($minBulkPrice, $maxBulkPrice) {
                            $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) >= ?', [$minBulkPrice]);
                            $wholesaleQuery->whereRaw('CAST(wholesale_price AS DECIMAL(10, 2)) <= ?', [$maxBulkPrice]);
                        });
                    });
                });
            }
           
        }
    
            $products = $query->paginate(6);
    
            return ProductResource::collection($products);
    }
}
