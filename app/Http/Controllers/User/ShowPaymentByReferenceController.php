<?php

namespace App\Http\Controllers\User;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;

class ShowPaymentByReferenceController extends Controller
{
    public function show($ref){
        $payment=Payment::where('transaction_ref',$ref)->where('user_id',2)->first();

        return response()->json(PaymentResource::make($payment));;
    }
}
