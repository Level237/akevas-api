<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentCoinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'price'=>$this->price,
            "user"=>$this->user->userName,
            "transaction_ref"=>$this->transaction_ref,
            "payment_of"=>$this->payment_of,
        ];
    }
}
