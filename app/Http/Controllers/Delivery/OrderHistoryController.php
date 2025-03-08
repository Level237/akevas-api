<?php

namespace App\Http\Controllers\Delivery;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function getOrderHistory(){
        $delivery=User::find(Auth::guard('api')->user()->id);
        $orders=$delivery->processOrders();
        return response()->json(OrderResource::collection($orders));
    }
}
