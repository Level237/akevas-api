<?php

namespace App\Http\Controllers\Coins;

use NotchPay\Payment;
use NotchPay\NotchPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class InitPaymentController extends Controller
{
    public function initPaymentCoin(Request $request){

        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{

            $payload=Payment::initialize([

                'amount' => $request->coins,
                'email' => Auth::guard('api')->user()->email,
                'name' => Auth::guard('api')->user()->firstName,
                'currency' => 'XAF',
                'reference' => Auth::guard('api')->user()->id . '-' . uniqid(),
                'user_id'=>Auth::guard('api')->user()->id,
                'callback' => 'https://cbed-129-0-76-158.ngrok-free.app/api/callback/payment?coins='.$request->coins.'&user_id='.Auth::guard('api')->user()->id.'&amount='.$request->amount.'&reference='.$Auth::guard('api')->user()->id . '-' . uniqid(),
            ]);
            
            return response()->json([
                'redirect_to' => $payload->authorization_url
            ]);
    
        }catch(Exception $e){
            return response()->json([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
