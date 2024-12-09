<?php

namespace App\Http\Controllers\Payment\Coolpay\Product;

use App\Events\MakePaymentEvent;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Subscription;
use App\Services\Payment\Coolpay\InitPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeProductController extends Controller
{
    public function initPayment(Request $request){

        $initResponse=(new InitPaymentService())->initPay($request->price,"Abonnement produit",$request->transaction_id,"56d271f5-8875-4911-b92d-0d12eb665ddf");

        return $initResponse;
    }

    public function paymentPending($membership_id,$product_id,$transaction_ref){
        $subscription_type=Subscription::find($membership_id);
        $product=Product::find($product_id);
        $paymentExist=Payment::where('transaction_ref',$transaction_ref)->first();

        if(!isset($paymentExist)){
            $data=[
                'payment_type'=>"Momo",
                'price'=>$subscription_type->subscription_price,
                'payment_of'=>"Abonnement Produit",
                'transaction_ref'=>$transaction_ref,
                'transaction_id'=>null,
                'membership_id'=>$membership_id,
                'product_id'=>$product_id,
                'status'=>"0",
                'user_id'=>Auth::user()->id
            ];
            $payment=event(new MakePaymentEvent($data));
            return response()->json(['message'=>"payment Pending"]);
    }
    }
}
