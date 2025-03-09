<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductByCategoryController extends Controller
{
    public function index($categoryId){
        $products=Product::with('categories')->whereHas('categories',function($query) use ($categoryId){
            $query->where('categories.id',$categoryId);
        })->paginate(12);
        return ProductResource::collection($products);
    }
}
