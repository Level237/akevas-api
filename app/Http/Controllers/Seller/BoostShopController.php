<?php

namespace App\Http\Controllers\Seller;

use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\SubscriptionUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BoostShopController extends Controller
{
    public function boost(Request $request){

        try{
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
        if($shop->shop_level==3){
            $shop->shop_level=4;
        }
        if($Subscription->id==3){
            $shop->coins=$shop->coins+500;
        }
        $hop->expire=null;
        $shop->isSubscribe=1;
        $shop->coins=$shop->coins-$request->coins;
        $shop->save();

        $payment=new Payment;
        $payment->payment_type="coins";
        $payment->price=$coins;
        $payment->payment_of="Boostage boutique";
        
        if($payment->save()){
        $subscription=new SubscriptionUser;
        $subscription->user_id=$shop->user_id;
        $subscription->subscription_id=$Subscription->id;
        $subscription->expire_at=$newDateTime;
        $subscription->status=1;
        $subscription->save();

        return response()->json(["status"=>1]);
        }else{
            return response()->json(["status"=>0]);
        }
        }catch(\Exception $e){
            return response()->json(["status"=>0]);
        }
        
    }
}
