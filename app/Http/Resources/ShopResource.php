<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'shop_name'=>$this->shop_name,
            'shop_description'=>$this->shop_description,
            'user'=>$this->user->name,
            'shop_key'=>$this->shop_key,
            'shop_type'=>$this->shopType->type_name,
            "status"=>$this->status,
            "isSubscribe"=>$this->isSubscribe,
            "expire"=>$this->expire,
            "subscribe_id"=>$this->subscribe_id
        ];
    }
}
