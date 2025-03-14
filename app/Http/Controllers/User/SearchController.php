<?php

namespace App\Http\Controllers\User;

use App\Models\Shop;
use App\Models\History;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search($query){

        $products=Product::where("product_name",'like',"%{$query}%")
        ->orWhere("product_description","like","%{$query}%")
        ->orWhere("product_price","like","%{$query}%")
        ->orWhere("product_price","like","%{$query}%")
        ->get();

        $shops=Shop::where("shop_name",'like',"%{$query}%")
        ->orWhere("shop_description","like","%{$query}%")
        ->orWhere("shop_description","like","%{$query}%")
        ->get();
        $user=Auth::guard('api')->user();
        if($user){
            $hystory=new History;
            $hystory->user_id=$user->id;
            $hystory->search_term=$query;
            $hystory->save();
        }

        return response()->json([
            'products' => $products,
            'shops' => $shops,
        ]);
    }
}
