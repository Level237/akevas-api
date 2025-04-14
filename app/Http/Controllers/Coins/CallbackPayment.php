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
        
            PaymentBackend::create([
                'payment_type'=>'coins',
                'price'=>$request->amount,
                'transaction_ref'=>$request->reference,
                'payment_of'=>'coins',
                'user_id'=>$request->user_id,
            ]);

            $shop=Shop::where('user_id',$request->user_id)->first();
            $shop->coins+=$request->amount;
            $shop->save();

            return redirect(env('FRONT_URL').'/checkout/state?coins='.$request->amount);
        }
    
}
