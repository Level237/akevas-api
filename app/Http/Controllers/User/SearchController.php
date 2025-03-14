<?php

namespace App\Http\Controllers\User;

use App\Models\Shop;
use App\Models\History;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;

class SearchController extends Controller
{
    public function search($query,$userId){

        $products=Product::where("product_name",'like',"%{$query}%")
        ->orWhere("product_description","like","%{$query}%")
        ->orWhere("product_price","like","%{$query}%")
        ->orWhere("product_price","like","%{$query}%")
        ->take(5)->get();

        $shops=Shop::where("shop_name",'like',"%{$query}%")
        ->orWhere("shop_description","like","%{$query}%")
        ->orWhere("shop_description","like","%{$query}%")
        ->take(5)->get();
        
        if($userId!=0){
            $hystory=new History;
            $hystory->user_id=$userId;
            $hystory->search_term=$query;
            $hystory->save();
        }

        return response()->json([
            'products' => ProductResource::collection($products),
            'shops' => ShopResource::collection($shops),
        ]);
    }
}
