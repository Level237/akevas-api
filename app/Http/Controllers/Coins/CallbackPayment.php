<?php

namespace App\Http\Controllers\Coins;

use App\Models\Shop;
use NotchPay\Payment;
use NotchPay\NotchPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment as PaymentBackend;

class CallbackPayment extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function callbackPayment(Request $request)
    {
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        $verifyTransaction = Payment::verify($request->get('reference'));
        if($verifyTransaction->transaction->status === 'canceled'){
            return response()->json(['status','canceled']);
        }else if($verifyTransaction->transaction->status === 'failed'){
            return response()->json(['status','failed']);
        }else if($verifyTransaction->transaction->status === 'pending'){
            return response()->json(['status','pending']);
        }else if($verifyTransaction->transaction->status === 'success'){
            PaymentBackend::create([
                'payment_type'=>'coins',
                'price'=>$request->get('amount'),
                'transaction_ref'=>$verifyTransaction->transaction->reference,
                'payment_of'=>'coins',
                'user_id'=>Auth::guard('api')->user()->id,
            ]);

            $shop=Shop::where('user_id',Auth::guard('api')->user()->id)->first();
            $shop->coins+=$request->get('amount');
            $shop->save();

            return redirect(env('FRONT_URL').'/checkout/state?coins='.$request->get('amount'));
        }
    }
}
