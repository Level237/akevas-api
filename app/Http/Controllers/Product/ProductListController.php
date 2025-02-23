<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductListController extends Controller
{
    public function index(){
        return ProductResource::collection(Product::orderBy('created_at', 'desc')->inRandomOrder()->take(4)->get());
    }

    public function adsProducts($id){
        return ProductResource::collection(Product::Where('subscribe_id',$id)->where('status',1)->inRandomOrder()->get());
    }
    public function allProducts(){
        return ProductResource::collection(Product::orderBy('created_at', 'desc')->paginate(6));
    }
}
