<?php

namespace App\Http\Controllers\Payment\Coolpay\Product;

use App\Events\MakePaymentEvent;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Subscription;
use App\Services\Payment\Coolpay\InitPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscribeProductController extends Controller
{
    public function initPayment(Request $request){

        $initResponse=(new InitPaymentService())->initPay($request->price,"Abonnement produit",$request->transaction_id,"56d271f5-8875-4911-b92d-0d12eb665ddf");

        return $initResponse;
    }

    public function initPaymentPending($membership_id,$product_id,$transaction_ref){
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
                'product_id'=>$product_id,
                'status'=>"1",
                'user_id'=>Auth::user()->id
            ];
            $payment=event(new MakePaymentEvent($data));
            return response()->json(['message'=>"payment Pending"]);
    }

    }
    public function paymentCallBack(Request $request){
        $payment=Payment::where('transaction_ref',$request->transaction_ref)
        ->where('payment_of','=',"productAds")->first();

        if($request->transaction_status==="SUCCESS"){
            $payment->status="2";
            $payment->save();
            $subscription=Subscription::find($payment->subscription_id);
            $product=Product::find($payment->product_id);
            $newDateTime = Carbon::now()->addDay(intval($subscription->subscription_duration));
            $newDateTime->setTimezone('Africa/Douala');
            $product->status=1;
            $product->isSubscribe=1;
            $product->expire=null;
            $product->subscribe_id=$subscription->id;

            if($product->save()){
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
