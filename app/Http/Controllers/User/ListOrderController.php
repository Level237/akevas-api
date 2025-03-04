<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;

class ListOrderController extends Controller
{
    public function listOrder(){
        $user=Auth::guard('api')->user();
        $orders=Order::where('user_id',$user->id)->orderBy('created_at','desc')->get();
        return response()->json(OrderResource::collection($orders));
    }
}
