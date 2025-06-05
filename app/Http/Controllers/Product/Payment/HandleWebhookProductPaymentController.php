<?php

namespace App\Http\Controllers\Product\Payment;

use App\Models\User;
use NotchPay\NotchPay;
use App\Models\Payment;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\OrderVariation;
use App\Models\ProductVariation;
use App\Models\VariationAttribute;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Payment\Verify\HandleVerifyPaymentNotchpay;

class HandleWebhookProductPaymentController extends Controller
{
    public function handleVerify(Request $request){
        NotchPay::setApiKey(env("NOTCHPAY_API_KEY"));
        $payload=$request->data;
        $reference = $payload['reference'];
        $merchant_reference=$payload['merchant_reference'];
        $hasVariation=$payload['hasVariation'];
        $productVariationId=$payload['productVariationId'];
        $attributeVariationId=$payload['attributeVariationId'];
        $productsPayments=$payload['productsPayments'];
        $shipping=$payload['shippingData'];
        $quarter_delivery=$payload['quarter_delivery'];
        $address=$payload['address'];
        $amount=$payload['amount'];
        $productId=$payload['productId'];

        try{
            $paymentStatus=(new HandleVerifyPaymentNotchpay())->verify($reference);
            if (isset($paymentStatus->status) && $paymentStatus->status == 'failed') {
                $userId = explode('-', $merchant_reference)[0];
                $user = User::find($userId);
                if (!Payment::where('transaction_ref', $reference)->exists()) {
                    if(isset($productsPayments)){
                        $order=$this->multipleOrder(
                        $userId,
                        $amount,
                        $shipping,
                        $quarter_delivery,
                        $address,
                        $productsPayments,
                        $hasVariation
                    );
                        return response()->json([
                            'success' => true,
                            'message' => 'Payment successful',
                            'order' => $order,
                        ], 200);
                    }else{
                        $order=$this->createOrder(
                        $userId,
                        $amount,
                        $shipping,
                        $productId,
                        $quantity,
                        $quarter_delivery,
                        $address,
                        $productVariationId,
                        $attributeVariationId
                    );
                    }
                }
            }
        }catch (\Exception $e) {
        Log::error('Webhook NotchPay failed', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'server error'], 500);
    }
    }

    private function createOrder($userId,$amount,$shipping,$productId,$quantity,$quarter_delivery,$address,$hasVariation,$productVariationId,$attributeVariationId){

        
        $order=new Order;
           $order->user_id=$userId;
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
            if($hasVariation==true){
                $orderVariation=new OrderVariation;
                if($attributeVariationId==null){
                    
                    $orderVariation->product_variation_id=$productVariationId;
                    $this->reduceQuantityProductVariation($productVariationId,$quantity);
                    
                }else{
                    $orderVariation->variation_attribute_id=$attributeVariationId;
                    $this->reduceQuantityAttributeVariation($attributeVariationId,$quantity);
                }

                    $orderVariation->variation_quantity=$quantity;
                    $orderVariation->variation_price=$amount;
                    $orderVariation->save();
            }else{
                $orderDetails=new OrderDetail;
               $orderDetails->order_id=$order->id;
               $orderDetails->product_id=$productId;
               $orderDetails->order_product_quantity=$quantity;
               $orderDetails->unit_price=$amount/$quantity;
               if($orderDetails->save()){
                   $this->reduceQuantity($productId,$quantity);
                   return $order;
               }
            }
               
           }
           return null;
   }

   private function multipleOrder($userId,$amount,$shipping,$quarter_delivery,$address,$productsPayments,$hasVariation){
           $order=new Order;
           $order->user_id=$userId;
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
               foreach($productsPayments as $product){
                if($product['hasVariation']==true){
                    $orderVariation=new OrderVariation;
                    if($product['attributeVariationId']==null){
                        $orderVariation->product_variation_id=$product['productVariationId'];
                        $this->reduceQuantityProductVariation($product['productVariationId'],$product['quantity']);
                        
                    }else{
                        $orderVariation->variation_attribute_id=$product['attributeVariationId'];
                        $this->reduceQuantityAttributeVariation($product['attributeVariationId'],$product['quantity']);
                    }

                    $orderVariation->variation_quantity=$product['quantity'];
                    $orderVariation->variation_price=$product['price'];
                    $orderVariation->save();
                }else{
                    $orderDetails=new OrderDetail;
                    $orderDetails->order_id=$order->id;
                    $orderDetails->product_id=$product['product_id'];
                    $orderDetails->order_product_quantity=$product['quantity'];
                    $orderDetails->unit_price=$product['price'];
                    if($orderDetails->save()){
                        $this->reduceQuantity($product['product_id'],$product['quantity']);
                        return $order;
                    }
                }
               }
              
           }
           return null;
   }

   private function reduceQuantity($productId,$quantity){
       $product=Product::find($productId);
       $product->product_quantity-=$quantity;
       $product->save();
   }

   private function reduceQuantityProductVariation($productVariationId,$quantity){
        $productVariation=ProductVariation::find($productVariationId);
        $productVariation->quantity-=$quantity;
        $productVariation->save();
   }

   private function reduceQuantityAttributeVariation($attributeVariationId,$quantity){
    $variationAttribute=VariationAttribute::find($attributeVariationId);
    $variationAttribute->quantity-=$quantity;
    $variationAttribute->save();
}
}
