<?php

namespace App\Http\Controllers\User;


use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ControlPaymentStatusByRefController extends Controller
{
    public function control(Request $request){
        $payment=Payment::where('transaction_ref',$request->reference)->where('user_id',Auth::guard('api')->user()->id)->first();
        Log::info("lld",[
            "le"=>Auth::guard('api')->user()->id,
            
        ]);
        if(isset($payment)){
            return response()->json(['status'=>200]);
        }else{
            return response()->json(['status'=>400]);
        }
    }
}
