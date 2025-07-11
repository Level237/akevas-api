<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\OrderResource;
class ListOrdersController extends Controller
{
    public function listOrders(){
        try{
            $orders=Order::orderBy('created_at','desc')->get();
        return response()->json(OrderResource::collection($orders));
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
              ], 500);
        }
    }
}
