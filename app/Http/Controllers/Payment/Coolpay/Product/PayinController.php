<?php

namespace App\Http\Controllers\Payment\Coolpay\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PayinController extends Controller
{
    public function payin(Request $request){
        $url = "https://my-coolpay.com/api/".env("PUBLIC_KEY_COOLPAY")."/payin";

        $response=Http::acceptJson()->withBody(
            json_encode(
                [
                    "customer_phone_number"=>$request->phone,
                    "transaction_amount"=>$request->amount,
                ]
            )
                )->post($url);

                $responseData=json_decode($response);

                return response()->json([
                    "status"=>$responseData->status,
                    "message"=>"Payment initiated",
                    "reference"=>$responseData->transaction_ref,
                    "statusCharge"=>$responseData->action
                ]);
    }
}
