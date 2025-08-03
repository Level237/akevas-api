<?php

namespace App\Http\Controllers\User;


use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaymentResource;

class RecentOrderController extends Controller
{
    public function recentOrders(){
        $user=Auth::guard('api')->user();
        $payments=Payment::where('user_id',$user->id)->orderBy('created_at','desc')->limit(3)->get();
        return response()->json(PaymentResource::collection($payments));
    }
}
