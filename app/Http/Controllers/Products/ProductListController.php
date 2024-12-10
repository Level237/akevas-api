<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductListController extends Controller
{
    public function index(){
        return ProductResource::collection(Product::orderBy('subscribe_id','DESC')->where('status',1)->get());
    }

    public function adsProducts($id){
        return ProductResource::collection(Product::Where('subscribe_id',$id)->where('status',1)->inRandomOrder()->get());
    }
}
