<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;

class RecentOrderController extends Controller
{
    public function recentOrder(){
        $orders=Order::orderBy('created_at','desc')->limit(3)->get();
        return response()->json(OrderResource::collection($orders));
    }
}
