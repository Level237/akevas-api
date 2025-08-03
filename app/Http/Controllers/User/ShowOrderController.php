<?php

namespace App\Http\Controllers\User;


use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentResource;

class ShowOrderController extends Controller
{
    public function showOrder($id){
        $payment=Payment::where('order_id',$id)->first();
        return response()->json(PaymentResource::make($payment));
    }
}
