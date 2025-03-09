<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StatOverviewController extends Controller
{
    public function getStatOverview(){
       try{
         $delivery=User::find(Auth::guard('api')->user()->id);
        $orders=$delivery->processOrders;
        $total_orders=$orders->count();
        $total_earnings=$orders->sum('fee_of_shipping');
        $average_duration = $orders->avg(function($order) {
            list($hours, $minutes, $seconds) = explode(':', $order->duration_of_delivery);
            return ($hours * 60) + $minutes + ($seconds / 60);
        });
        // Arrondir à 2 décimales
        $average_duration = round($average_duration, 2);
        return response()->json([
      
                'total_orders' => $total_orders,
                'total_earnings' => $total_earnings." XAF",
                'average_duration' => $average_duration." minutes"

        ]);
       }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e
              ], 500);
        }
    }
}
