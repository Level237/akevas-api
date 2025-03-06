<?php

namespace App\Http\Controllers\Payment\Stripe;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function pay(Request $request)
    {
         Stripe::setApiKey(env('STRIPE_SECRET'));

        try{
            $charge = Charge::create([
                'amount' => $this->calculateRealNumber($request->amount), // Montant en cents
                'currency' => 'eur',
                'source' => $request->stripeToken,
                'description' => 'Achat d\'un produit',
            ]);

            if(isset($request->productsPayments)){
                foreach($request->productsPayments as $product){
                    $order=$this->createOrder($request->amount,
                    $request->shipping,
                    $product['product_id'],
                    $product['quantity'],
                    $product['price'],
                    $request->quarter_delivery);
                }
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
                $request->quarter_delivery);
                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful',
                    'order' => $order,
                ], 200);
            }
            
           
        }catch(\Exception $e){
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
    private function createOrder($amount,$shipping,$productId,$quantity,$price,$quarter_delivery){

         $order=new Order;
            $order->user_id=Auth::guard("api")->user()->id;
            $order->isPay=1;
            $order->total=$amount;
            $order->fee_of_shipping=$shipping;
            $order->payment_method="0";
            if(isset($quarter_delivery)){
                $order->quarter_delivery=$quarter_delivery;
            }
            if($order->save()){
                $orderDetails=new OrderDetail;
                $orderDetails->order_id=$order->id;
                $orderDetails->product_id=$productId;
                $orderDetails->order_product_quantity=$quantity;
                $orderDetails->unit_price=$price;
                if($orderDetails->save()){
                    $this->reduceQuantity($productId,$quantity);
                    return $order;
                }
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
