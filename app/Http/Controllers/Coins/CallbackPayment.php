<?php

namespace App\Http\Controllers\Coins;

use Log;
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
        
        
            
        Log::info('Callback reçu', $request->all());
        dd('callback atteint', $request->all());
            $shop=Shop::where('user_id',"16")->first();
            $shop->coins+=10000;
            $shop->save();

            //return redirect(env('FRONT_URL').'/checkout/state?coins='.$request->amount);
        }
    
}
