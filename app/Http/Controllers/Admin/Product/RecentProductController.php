<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class RecentProductController extends Controller
{
    public function index(){
        $products = Product::orderBy('created_at', 'desc')->take(6)->get();

        return ProductResource::collection($products);

    }
}
