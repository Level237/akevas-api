<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductListController extends Controller
{
    public function index(){
        return ProductResource::collection(Product::inRandomOrder()->where('status',1)->where('is_trashed',0)->take(5)->get());
    }   

    public function adsProducts($id){
        return ProductResource::collection(Product::inRandomOrder()->Where('subscribe_id',$id)->where('status',1)->where('is_trashed',0)->get());
    }
    public function allProducts(){
        return ProductResource::collection(Product::inRandomOrder()->where('status',1)->where('is_trashed',0)->paginate(6));
    }
}
