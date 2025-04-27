<?php

namespace App\Http\Controllers\Seller;

use App\Models\Shop;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class CatalogueController extends Controller
{
    public function index($shop_key){
        $shop = Shop::where('shop_key', $shop_key)->first();
        if(!$shop){
            return response()->json(['message' => 'Shop not found'], 404);
        }
        $products = Product::where('shop_id', $shop->id)->get();
        return response()->json(ProductResource::collection($products));
    }
}
