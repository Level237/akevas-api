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
    public function PaymentCoin(Request $request){
        $reference=Auth::guard('api')->user()->id . '-' . uniqid();
        $response=$this->initPayment($request,$reference);
        $responseCharge=$this->charge($response);
        return response()->json([
            "status"=>"success",
            "message"=>"Payment initiated",
            "reference"=>$response,
            "statusCharge"=>$responseCharge
        ]);
    }

    private function initPayment(Request $request,$reference){
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{

            $url = "https://api.notchpay.co/payments/initialize";

            $urlCallback="http://localhost:5173/coins/confirmation";
                
           
            
        $response=Http::acceptJson()->withBody(json_encode(
            [
                "email"=>Auth::guard('api')->user()->email,
                "amount"=>$request->coins,
                "currency"=>"XAF",
                "reference"=>$reference,
                "callback"=>$urlCallback,
            ]
            ),'application/json')->withHeaders([
                "Authorization"=>"pk.GIliU6f6km4eymwifBDdyPPPdVFUbK8IDidMChFVGcxcKQrw36bi0H63gcRHLIVZeLh9MiFw20xhJgYrM7iWNC38s6dMcDXGaBJDLFVIr6pOWXgRiL4pv6xmSi6nf"
            ])->post($url);
            $responseData=json_decode($response);
            return $responseData->transaction->reference;
    
        }catch(Exception $e){
            return response()->json([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],500);
        }
    }
    private function charge($reference){
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{
            $url = "https://api.notchpay.co/payments/".$reference;
            $response=Http::acceptJson()->withBody(json_encode(
                [
                    "channel" => "cm.orange",
                    "data" => [
                        "phone" => "+237690394365"
                    ]
                    ],
                ),'application/json')->withHeaders([
                    "Authorization"=>"pk.GIliU6f6km4eymwifBDdyPPPdVFUbK8IDidMChFVGcxcKQrw36bi0H63gcRHLIVZeLh9MiFw20xhJgYrM7iWNC38s6dMcDXGaBJDLFVIr6pOWXgRiL4pv6xmSi6nf"
                ])->post($url);
                $responseData=json_decode($response);
                return $responseData->status;
        }catch(Exception $e){
            return response()->json([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],500);
        }
    }
}
