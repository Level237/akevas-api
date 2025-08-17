<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $relatedPayments = Payment::where('transaction_ref', $this->transaction_ref)->get();
        $order_ids = $relatedPayments->pluck('order_id')->unique()->filter()->all();
        $orders = Order::whereIn('id', $order_ids)->get();

        return [
            'id'=>$this->id,
            'price'=>$this->price,
            "user"=>$this->user->userName,
            "transaction_ref"=>$this->transaction_ref,
            "payment_of"=>$this->payment_of,
            "order"=>OrderResource::collection($orders)
        ];
    }
}
