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
        $responseCharge=$this->charge($response,$request->phone,$request->paymentMethod);
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

            $urlCallback="https://api-akevas.akevas.com/api/notchpay/coins/webhook";
                
           
            
        $response=Http::acceptJson()->withBody(json_encode(
            [
                "email"=>Auth::guard('api')->user()->email,
                "amount"=>"100",
                "currency"=>"XAF",
                "reference"=>$reference,
                "phone"=>$request->phone,
                "callback"=>$urlCallback,
                
            ]
            ),'application/json')->withHeaders([
                "Authorization"=>env("NOTCHPAY_API_KEY")
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
    private function charge($reference,$phone,$provider){
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{
            $url = "https://api.notchpay.co/payments/".$reference;
            $response=Http::acceptJson()->withBody(json_encode(
                [
                    "channel" =>$provider,
                    "data" => [
                        "phone" => "+237".$phone
                    ]
                    ]
                ),'application/json')->withHeaders([
                    "Authorization"=>env("NOTCHPAY_API_KEY")
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