<?php

namespace App\Http\Controllers\Shops;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopProfileResource;

class GetProfileShopController extends Controller
{
    public function getProfile(){
        return response()->json(ShopProfileResource::collection(
            Shop::inRandomOrder()->take(6)->get()));
    }
}
