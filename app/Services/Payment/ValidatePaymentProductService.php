<?php

namespace App\Services\Payment;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\OrderVariation;
use App\Models\ProductVariation;
use App\Models\VariationAttribute;
use Illuminate\Support\Facades\Log;
use App\Services\Payment\Verify\HandleVerifyPaymentNotchpay;

class ValidatePaymentProductService
{

    public function handle(
    $request,
    $userId
    )
    {
        
        

            
            $user = User::find($userId);
            if (!Payment::where('transaction_ref', $request['reference'])->exists()) {

                Payment::create([
                    'payment_type' => 'product',
                    'price' => $request['amount'],
                    'transaction_ref' => $request['reference'],
                    'payment_of' => 'Paiement produit',
                    'user_id' => $user->id,
                ]);
                if(isset($request['productsPayments'])){
                    $order=$this->multipleOrder(
                        $userId,
                        $request['amount'],
                        $request['shipping'],
                        $request['quarter_delivery'],
                        $request['address'],
                        $request['productsPayments'],
                        $request['reference']
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment successful',
                        'order' => $order,
                    ], 200);
                }else{

                    
                    $order=$this->createOrder(
                    $userId,
                    $request['amount'],
                    $request['shipping'],
                    $request['productId'],
                    $request['quantity'],
                    $request['quarter_delivery'],
                    $request['address'],
                    $request['hasVariation'],
                    $request['productVariationId'],
                    $request['attributeVariationId'],
                    $request['reference']
                );
            }
        
    
    }
}

private function createOrder($userId,$amount,$shipping,$productId,$quantity,$quarter_delivery,$address,$hasVariation,$productVariationId,$attributeVariationId,$reference){

        
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

        $this->savePaymentAndOrder($reference,$order->id);
        if($hasVariation=="false"){

            $orderDetails=new OrderDetail;
           $orderDetails->order_id=$order->id;
           $orderDetails->product_id=$productId;
           $orderDetails->order_product_quantity=$quantity;
           $orderDetails->unit_price=$amount/$quantity;
           if($orderDetails->save()){
               $this->reduceQuantity($productId,$quantity);
               return $order;
           }
            
        }else{
            $orderVariation=new OrderVariation;
            $orderVariation->order_id=$order->id;
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
        }
           
       }
       return null;
}

private function multipleOrder($userId,$amount,$shipping,$quarter_delivery,$address,$productsPayments,$reference){
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
        $this->savePaymentAndOrder($reference,$order->id);
        foreach($productsPayments as $product){
         if($product['hasVariation']=="true"){
             $orderVariation=new OrderVariation;
             $orderVariation->order_id=$order->id;
             if($product['attributeVariationId']==null){
                 $orderVariation->product_variation_id=$product['productVariationId'];
                 $this->reduceQuantityProductVariation($product['productVariationId'],$product['quantity']);
                 
             }else{
                 $orderVariation->variation_attribute_id=$product['attributeVariationId'];
                 $this->reduceQuantityAttributeVariation($product['attributeVariationId'],$product['quantity']);
             }

             $orderVariation->variation_quantity=$product['quantity'];
             $orderVariation->variation_price=$product['price'];
             Log::info('Order variation create success');
             $orderVariation->save();
         }else{
             $orderDetails=new OrderDetail;
             $orderDetails->order_id=$order->id;
             $orderDetails->product_id=$product['product_id'];
             $orderDetails->order_product_quantity=$product['quantity'];
             $orderDetails->unit_price=$product['price'];
             if($orderDetails->save()){
                 $this->reduceQuantity($product['product_id'],$product['quantity']);
                 Log::info('Order create success');
                
             }
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

private function reduceQuantityProductVariation($productVariationId,$quantity){
    $productVariation=ProductVariation::find($productVariationId); 
    Log::info('reduce price product variation');
    $productVariation->quantity-=$quantity;
    $productVariation->save();
}

private function reduceQuantityAttributeVariation($attributeVariationId,$quantity){
$variationAttribute=VariationAttribute::find($attributeVariationId);
$variationAttribute->quantity-=$quantity;
$variationAttribute->save();
}

private function savePaymentAndOrder($ref,$order_id){

    $payment=Payment::where('transaction_ref',$ref)->first();

    $payment->order_id=$order_id;
    $payment->save();
}

}
