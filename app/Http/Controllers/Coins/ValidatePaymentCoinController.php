<?php

namespace App\Http\Controllers\Coins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Payment\ValidatePaymentCoinService;

class ValidatePaymentCoinController extends Controller
{
    public function handle(Request $request){

        $userId=Auth::guard('api')->user()->id;
        (new ValidatePaymentCoinService())->handle($request->reference,$request->amount,$userId);
    }
}
