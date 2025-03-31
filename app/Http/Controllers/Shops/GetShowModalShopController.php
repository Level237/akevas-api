<?php

namespace App\Http\Controllers\Shops;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ModalShopResource;

class GetShowModalShopController extends Controller
{
    public function showRandom(){
        return response()->json(
            Shop::inRandomOrder()->first());
    }
}
