<?php

namespace App\Http\Controllers\Payment\Coolpay\Product;

use Illuminate\Http\Request;
use App\Jobs\PaymentProcessingJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PayinController extends Controller
{
    public function payin(Request $request){
        try{
            $userId=Auth::guard('api')->user()->id;
            $url = "https://my-coolpay.com/api/".env("PUBLIC_KEY_COOLPAY")."/payin";

        $response=Http::acceptJson()->withBody(
            json_encode(
                [
                    "customer_phone_number"=>$request->paymentPhone,
                    "transaction_amount"=>$request->payinAmount,
                ]
            )
                )->post($url);

                $responseData=json_decode($response);

                if(isset($responseData->message ) && $responseData->message == "Le solde du compte du payeur est insuffisant."){
                    return response()->json([
                        "status"=>"low",
                        "message"=>"Le solde du compte du payeur est insuffisant.",
                    ]);
                }else{
                    Log::info('PayinController: payin', [
                        'response' => $responseData->transaction_ref
                    ]);
                    PaymentProcessingJob::dispatch($request->all(),$userId,$responseData->transaction_ref);

                    return response()->json([
                        "status"=>$responseData->status,
                        "message"=>"Payment initiated",
                        "reference"=>$responseData->transaction_ref,
                        "statusCharge"=>$responseData->action
                    ]);
                }
               
        }catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
