<?php

namespace App\Http\Controllers\Seller;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaymentCoinResource;

class ShowPaymentByRefController extends Controller
{
    public function show($ref){
        $payment=Payment::where('transaction_ref',$ref)
        ->where('user_id',Auth::guard('api')->user()->id)
        ->where('payment_of','coins')->first();

        return response()->json(PaymentCoinResource::make($payment));
    }
}
