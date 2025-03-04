<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\OrderResource;
class ListOrdersController extends Controller
{
    public function listOrders(){
        $orders=Order::orderBy('created_at','desc')->get();
        return response()->json(OrderResource::collection($orders));
    }
}
