<?php

namespace App\Http\Controllers\User;


use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ControlPaymentStatusByRefController extends Controller
{
    public function control(Request $request){
        $payment=Payment::where('transaction_ref',$request->ref)->where('user_id',Auth::guard('api')->user()->id)->first();

        if(isset($payment)){
            return response()->json(['status'=>200]);
        }else{
            return response()->json(['status'=>400]);
        }
    }
}
