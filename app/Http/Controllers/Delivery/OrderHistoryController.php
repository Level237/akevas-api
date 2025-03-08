<?php

namespace App\Http\Controllers\Delivery;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;

class OrderHistoryController extends Controller
{
    public function getOrderHistory(){
        $delivery=User::find(Auth::guard('api')->user()->id);
        $orders=$delivery->processOrders;
        return response()->json(OrderResource::collection($orders));
    }
}
