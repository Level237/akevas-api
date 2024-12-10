<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TakeOrderProcessController extends Controller
{
    public function take($order_id){

        $newDelivery=new Delivery;
        $newDelivery->order_id=$order_id;
        $newDelivery->user_id=Auth::guard('api')->user()->id;
        return response()->json(["message"=>"Order accept"]);
    }
}
