<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ProductListController extends Controller
{
    public function index(){
        return ProductResource::collection(Product::inRandomOrder()->where('status',1)->where('is_trashed',0)->take(5)->get());
    }   

    public function adsProducts($id){
        return ProductResource::collection(Product::inRandomOrder()->Where('subscribe_id',$id)->where('status',1)->where('is_trashed',0)->get());
    }
    public function allProducts(Request $request){
        $query = Product::inRandomOrder()
        ->where('status', 1)
        ->where('is_trashed', 0);

        
       // Appliquer le filtre de prix si des paramètres sont présents
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

    if ($request->has('categories')) {
        $categoryIdsString = $request->input('categories');
        $categoryIds = explode(',', $categoryIdsString);

        $query->whereHas('categories', function (Builder $categoryQuery) use ($categoryIds) {
            $categoryQuery->whereIn('categories.id', $categoryIds); // <-- Corrected
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

        $products = $query->paginate(6);

        return ProductResource::collection($products);
    }
}
