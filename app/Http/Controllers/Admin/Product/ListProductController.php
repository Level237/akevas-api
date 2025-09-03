<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ListProductController extends Controller
{
    public function index(){
        $products = Product::orderBy('created_at', 'desc')->where('is_trashed',0)->get();

        return ProductResource::collection($products);
    }
}
