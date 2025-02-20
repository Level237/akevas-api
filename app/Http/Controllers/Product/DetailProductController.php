<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class DetailProductController extends Controller
{
    public function index($product_url){
        $product=Product::where('product_url',$product_url)->first();
        return ProductResource::make($product);
    }
}
