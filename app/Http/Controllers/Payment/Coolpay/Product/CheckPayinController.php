<?php

namespace App\Http\Controllers\Payment\Coolpay\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class CheckPayinController extends Controller
{
    public function checkStatus(Request $request){

        $url = "https://my-coolpay.com/api/".env("PUBLIC_KEY_COOLPAY")."/checkStatus/".$request->transaction_ref;

        $response=Http::get($url);
        $responseData=json_decode($response);

        Log::info('CheckPayinController: checkStatus', [
            'response' => $response
        ]);
        return response()->json(['status' => $responseData->transaction_status]);
    }
    
}
