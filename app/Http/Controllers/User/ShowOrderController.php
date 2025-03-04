<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;

class ShowOrderController extends Controller
{
    public function showOrder($id){
        $order=Order::find($id);
        return response()->json(OrderResource::make($order));
    }
}
