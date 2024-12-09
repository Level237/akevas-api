<?php

namespace App\Http\Controllers\Payment\Coolpay\Shop;

use App\Events\MakePaymentEvent;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\Subscription;
use App\Services\Payment\Coolpay\InitPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeShopController extends Controller
{
    public function initPayment(Request $request){

        $initResponse=(new InitPaymentService())->initPay($request->price,"Abonnement boutique",$request->transaction_id,"424ed393-92b5-4ccb-b3d4-fe0465d33025");

        return $initResponse;
    }

    public function initPaymentPending($membership_id,$shop_id,$transaction_ref){
        $subscription_type=Subscription::find($membership_id);
        $paymentExist=Payment::where('transaction_ref',$transaction_ref)->first();

        if(!isset($paymentExist)){
            $data=[
                'payment_type'=>"Momo",
                'price'=>$subscription_type->subscription_price,
                'payment_of'=>"shopAds",
                'transaction_ref'=>$transaction_ref,
                'transaction_id'=>null,
                'membership_id'=>$membership_id,
                'shop_id'=>$shop_id,
                'status'=>"1",
                'user_id'=>Auth::user()->id
            ];
            $payment=event(new MakePaymentEvent($data));
            return response()->json(['message'=>"payment Pending"]);
    }

    }
    public function paymentCallBack(Request $request){
        $payment=Payment::where('transaction_ref',$request->transaction_ref)
        ->where('payment_of','=',"shopAds")->first();

        if($request->transaction_status==="SUCCESS"){
            $payment->status="2";
            $payment->save();
            $subscription=Subscription::find($payment->subscription_id);
            $shop=Shop::find($payment->shop_id);
            $newDateTime = Carbon::now()->addDay(intval($subscription->subscription_duration));
            $newDateTime->setTimezone('Africa/Douala');
            $shop->status=1;
            $shop->isSubscribe=1;
            $shop->expire=null;
            $shop->subscribe_id=$subscription->id;

            if($shop->save()){
                //DB::table('memberships_users')->insert([
                    //'user_id'=>$payment->user_id,
                    //'membership_id'=>$membership->id,
                    //'payment_id'=>$payment->id,
                    //'expire_at'=>$newDateTime,
                    //'announcement_id'=>$announcement->id,
                    //'status'=>1
                //]);
            }
        }else if($request->transaction_status==="FAILED"){
            $payment->status="1";
            $payment->save();
        }
    }
}
