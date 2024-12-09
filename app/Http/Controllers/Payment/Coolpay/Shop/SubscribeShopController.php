<?php

namespace App\Http\Controllers\Payment\Coolpay\Shop;

use App\Events\MakePaymentEvent;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\Subscription;
use App\Services\Payment\Coolpay\InitPaymentService;
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
                'payment_of'=>"productAds",
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
}
