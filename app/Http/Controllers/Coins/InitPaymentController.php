<?php

namespace App\Http\Controllers\Coins;

use NotchPay\Payment;
use NotchPay\NotchPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class InitPaymentController extends Controller
{
    public function initPaymentCoin(Request $request){

        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{

            $payload=Payment::initialize([

                'amount' => $request->coins,
                'email' => Auth::guard('api')->user()->email,
                'name' => Auth::guard('api')->user()->name,
                'currency' => 'XAF',
                'reference' => Auth::guard('api')->user()->id . '-' . uniqid(),
                'callback' => route('notchpay-callback'),
                'description' => $product->description,
            ]);
            $url = "https://api.notchpay.co/payments/initialize";
            $coins=$request->coins;
            $urlCallback="";
            $response=Http::acceptJson()->withBody(json_encode(
                [
                    "email"=>"brams23@gmail.com",
                    "amount"=>"100",
                    "currency"=>"XAF",
                    "reference"=>"COINID".rand(123456, 999999),
                    "callback"=>$urlCallback,
                ]
                ),'application/json')->withHeaders([
                    "Authorization"=>""
                ])->post($url);
    
                return json_decode($response);
        }catch(Exception $e){
            return response()->json([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
