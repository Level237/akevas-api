<?php

namespace App\Http\Controllers\Product\Payment;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Product\Payment\SucessPaymentController;

class SucessPaymentController extends Controller
{
    public function successPayment(Request $request)
    {
        

        try{
            $existingOrder = Order::where('user_id', Auth::guard("api")->user()->id)
                             ->where('total', $request->amount)
                             ->where('created_at', '>=', now()->subMinutes(5))
                             ->first();

        if ($existingOrder) {
            return response()->json([
                'success' => true,
                'message' => 'Order already processed',
                'order' => $existingOrder,
            ], 200);
        }

            if(isset($request->productsPayments)){
               
                
                    $order=$this->multipleOrder($request);
                
             return response()->json([
                'success' => true,
                'message' => 'Payment successful',
                'order' => $order,
            ], 200);
            }else{
                    $order=$this->createOrder($request->amount,
                    $request->shipping,
                    $request->productId,
                    $request->quantity,
                    $request->price,
                    $request->quarter_delivery,
                    $request->address);
                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful',
                    'order' => $order,
                ], 200);
            }
            
           
        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e
              ], 500);
        }
    }

    function calculateRealNumber($amount) {
        return (($amount)*100);
    }
    private function createOrder($amount,$shipping,$productId,$quantity,$price,$quarter_delivery,$address){

        
         $order=new Order;
            $order->user_id=Auth::guard("api")->user()->id;
            $order->isPay=1;
            $order->total=$amount;
            $order->fee_of_shipping=$shipping;
            $order->payment_method="0";
            if(isset($quarter_delivery)){
                $order->quarter_delivery=$quarter_delivery;
            }
            if(isset($address)){
                $order->address=$address;
            }
            if($order->save()){
                $orderDetails=new OrderDetail;
                $orderDetails->order_id=$order->id;
                $orderDetails->product_id=$productId;
                $orderDetails->order_product_quantity=$quantity;
                $orderDetails->unit_price=$price/$quantity;
                if($orderDetails->save()){
                    $this->reduceQuantity($productId,$quantity);
                    return $order;
                }
            }
            return null;
    }

    private function multipleOrder($request){
            $order=new Order;
            $order->user_id=Auth::guard("api")->user()->id;
            $order->isPay=1;
            $order->total=$request->amount;
            $order->fee_of_shipping=$request->shipping;
            $order->payment_method="0";
            if(isset($request->quarter_delivery)){
                $order->quarter_delivery=$request->quarter_delivery;
            }
            if(isset($request->address)){
                $order->address=$request->address;
            }
            if($order->save()){
                foreach($request->productsPayments as $product){
                    $orderDetails=new OrderDetail;
                    $orderDetails->order_id=$order->id;
                    $orderDetails->product_id=$product['product_id'];
                    $orderDetails->order_product_quantity=$product['quantity'];
                    $orderDetails->unit_price=$product['price'];
                    if($orderDetails->save()){
                        $this->reduceQuantity($product['product_id'],$product['quantity']);
                    }
                }
                return $order;
            }
            return null;
    }

    private function reduceQuantity($productId,$quantity){
        $product=Product::find($productId);
        $product->product_quantity-=$quantity;
        $product->save();
    }

    public function getOrders($quarter_name)
    {
        $delivery = User::where('role_id', 4)
            ->with(['vehicles.quarters' => function($query) use ($quarter_name) {
                $query->where('quarter_name', $quarter_name);
            }])
            ->whereHas('vehicles.quarters', function($query) use ($quarter_name) {
                $query->where('quarter_name', $quarter_name);
            })
            ->get();
        
        return $delivery;
    }
}
