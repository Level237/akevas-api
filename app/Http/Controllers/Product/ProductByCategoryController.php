<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductByCategoryController extends Controller
{
    public function index($categoryUrl){
        $products=Product::with('categories')->whereHas('categories',function($query) use ($categoryUrl){
            $query->where('categories.category_url',$categoryUrl);
        })->get();
        return response()->json(ProductResource::collection($products));
    }
}
