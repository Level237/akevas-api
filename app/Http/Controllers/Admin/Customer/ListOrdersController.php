<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Http\Resources\PaymentResource;
class ListOrdersController extends Controller
{
    public function listOrders(){
        try{
            $orders=Payment::where('order_id','!=',null)->orderBy('created_at','desc')->get();
        return response()->json(PaymentResource::collection($orders));
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
              ], 500);
        }
    }
}
