<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;

class OrderDetailController extends Controller
{
    public function detail($id){
        $order=Order::find($id);
        return response()->json(OrderResource::make($order));
    }
}
