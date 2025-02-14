<?php

namespace App\Http\Controllers\Admin\Seller;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfirmStatusSellerController extends Controller
{
    public function index($shop_id,Request $request){
        $shop=Shop::find($shop_id);
        $shop->isPublished=$request->isPublished;
        $shop->state=$request->state;
        if($shop->save){
            $user=User::find($shop->user_id);
            $user->isSeller=$request->isSeller;
            $user->save();
            return response()->json(['message'=>"success"]);
        }
    }
}
