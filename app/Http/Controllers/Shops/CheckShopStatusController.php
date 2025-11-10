<?php

namespace App\Http\Controllers\Shops;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CheckShopStatusController extends Controller
{
    public function checkShopStatus(Request $request){
        $user=User::where("email",$request->email)->first();

        if(!$user){
           return response()->json(['exists' => false]);
        }

        $shop=Shop::where("user_id",$user->id)->exists();

        return response()->json(['exists' => $shop]);
    }
}
