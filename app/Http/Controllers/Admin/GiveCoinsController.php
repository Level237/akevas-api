<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GiveCoinsController extends Controller
{
    public function giveCoins(Request $request){
        $shop=Shop::find($request->shop_id);
        $shop->coins=$shop->coins+$request->coins;

        return response()->json(["message"=>"success"]);
    }
}
