<?php

namespace App\Http\Controllers\Shops;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ModalShopResource;

class GetShowModalShopController extends Controller
{
    public function showRandom(){
        return response()->json(ModalShopResource::make(
            Shop::orderBy('created_at', 'desc')->inRandomOrder()->take(1)->first()));
    }
}
