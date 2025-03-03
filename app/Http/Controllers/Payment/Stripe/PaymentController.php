<?php

namespace App\Http\Controllers\Payment\Stripe;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Order;
use App\Models\Payment;
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
            foreach($request->productsPayments as $product){
                $order=new Order;
            $order->user_id=Auth::guard("api")->user()->id;
            $order->isPay=1;
            $order->total=$request->amount;
            $order->fee_of_shipping=$request->shipping;
            $order->payment_method="0";
            if($order->save()){
                $orderDetails=new OrderDetail;
                $orderDetails->order_id=$order->id;
                $orderDetails->product_id=$product['product_id'];
                $orderDetails->order_product_quantity=$product['quantity'];
                $orderDetails->unit_price=$product['price'];
                $orderDetails->save();
            }
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
}
