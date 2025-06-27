<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;

class ControlPaymentStatusByRefController extends Controller
{
    public function control(Request $request){
        $payment=Payment::where('transaction_ref',$request->ref)->where('user_id',Auth::guard('api')->user()->id)->first();

        if(isset($payment)){
            $order=Order::find($payment->order_id);
            response()->json(OrderResource::collection($order));
        }
    }
}
