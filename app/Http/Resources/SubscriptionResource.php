<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'subscription_name'=>$this->subscription_name,
            'subscription_price'=>$this->subscription_price,
            'subscription_duration'=>$this->subscription_duration,
            'descriptions'=>$this->descriptions,
            'created_at'=>$this->created_at,
        ];
    }
}
