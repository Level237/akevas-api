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
        if ($request->has('min_price') || $request->has('max_price')) {
            $query->where(function (Builder $subQuery) use ($request) {
                
                // Logique pour les produits simples
                $subQuery->where(function ($simpleProductQuery) use ($request) {
                    if ($request->has('min_price')) {
                        $minPrice = floatval($request->input('min_price'));
                        $simpleProductQuery->whereRaw('CAST(product_price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    }
                    if ($request->has('max_price')) {
                        $maxPrice = floatval($request->input('max_price'));
                        $simpleProductQuery->whereRaw('CAST(product_price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                    }
                });
    
                // Logique pour les produits variés
                $subQuery->orWhereHas('variations', function (Builder $variationQuery) use ($request) {
                    if ($request->has('min_price')) {
                        $minPrice = floatval($request->input('min_price'));
                        $variationQuery->whereRaw('CAST(price AS DECIMAL(10, 2)) >= ?', [$minPrice]);
                    }
                    if ($request->has('max_price')) {
                        $maxPrice = floatval($request->input('max_price'));
                        $variationQuery->whereRaw('CAST(price AS DECIMAL(10, 2)) <= ?', [$maxPrice]);
                    }
                });
            });
        }


        $products = $query->paginate(6);

        return ProductResource::collection($products);
    }
}
