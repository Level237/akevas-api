<?php

namespace App\Http\Controllers\Shops;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopListController extends Controller
{
    public function index(){
        return ShopResource::collection(Shop::orderBy('subscribe_id','DESC')->where('status',1)->get());
    }

    public function adsShops($id){
        return ShopResource::collection(Shop::Where('subscribe_id',$id)->where('status',1)->inRandomOrder()->get());
    }
}
