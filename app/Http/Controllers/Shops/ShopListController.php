<?php

namespace App\Http\Controllers\Shops;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopListController extends Controller
{
    public function index(){
        return ShopResource::collection(
            Shop::inRandomOrder()->where('state',1)->take(7)->get());
    }

    public function all(){
        return ShopResource::collection(Shop::where('state',1)->orderBy('created_at', 'desc')->paginate(6));
    }

    public function adsShops($id){
        return ShopResource::collection(Shop::where('state',1)->where('subscribe_id',$id)->where('status',1)->inRandomOrder()->get());
    }
}
