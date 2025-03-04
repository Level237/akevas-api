<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\OrderDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'total_amount'=>$this->total,
            'status'=>$this->status,
            'created_at'=>$this->created_at,
           'quarter_delivery'=>$this->quarter_delivery,
           'order_details'=>OrderDetailResource::collection($this->orderDetails)        ,
            'itemsCount'=>$this->orderDetails->count(),
        ];
    }
}
