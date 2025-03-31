<?php

namespace App\Http\Controllers\Shops;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetShowModalShopController extends Controller
{
    public function showRandom(){
        return response()->json(ShopProfileResource::make(
            Shop::orderBy('created_at', 'desc')->inRandomOrder()->take(1)->first()));
    }
}
