<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "product_name"=>$this->product_name,
            "product_description"=>$this->product_description,
            "shop_name"=>$this->shop->shop_name,
            "product_url"=>$this->product_url,
            "product_price"=>$this->product_price,
            "product_quantity"=>$this->product_quantity,
            "status"=>$this->status,
            "isSubscribe"=>$this->isSubscribe,
            "expire"=>$this->expire,
            "subscribe_id"=>$this->subscribe_id
        ];
    }
}
