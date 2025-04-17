<?php

namespace App\Http\Controllers\Seller;

use App\Models\Shop;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BoostShopController extends Controller
{
    public function boost(Request $request){
        $shop = Shop::where('user_id', Auth::guard('api')->user()->id)->first();
        $Subscription=Subscription::find($request->subscription_id);
        if(!shop){
            return response()->json([
                'message' => 'Shop not found'
            ], 404);
        }

        $newDateTime = Carbon::now()->addDay(intval($Subscription->subscription_duration));
        $newDateTime->setTimezone('Africa/Douala');

        $shop->subscription_id = $Subscription->id;
        $hop->expire=null;
        $shop->isSubscribe=1;
        $shop->save();
        
    }
}
