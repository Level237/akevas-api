<?php

namespace App\Http\Controllers\Product\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use NotchPay\Payment;
use NotchPay\NotchPay;
class InitPaymentController extends Controller
{
    public function initPayment(Request $request){
        $reference=Auth::guard('api')->user()->id . '-' . uniqid();
        $response=$this->initPaymentProcess($request,$reference);
        $responseCharge=$this->charge($response);
        return response()->json([
            "status"=>"success",
            "message"=>"Payment initiated",
            "reference"=>$response,
            "statusCharge"=>$responseCharge
        ]);
    }

    private function initPaymentProcess(Request $request,$reference){
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        try{

            $url = "https://api.notchpay.co/payments/initialize";

        $response=Http::acceptJson()->withBody(json_encode(
            [
                "email"=>Auth::guard('api')->user()->email,
                "amount"=>"100",
                "productId"=>$request->productId,
                "quantity"=>$request->quantity,
                "price"=>$request->price,
                "quarter_delivery"=>$request->quarter_delivery,
                "address"=>$request->address,
                "shippingData"=>$request->shipping,
                "currency"=>"XAF",
                "reference"=>$reference,
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
