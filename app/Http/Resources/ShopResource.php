<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
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
            'shop_id'=>$this->id,
            'shop_name'=>$this->shop_name,
            'shop_description'=>$this->shop_description,
            'shop_profile'=>URL("/storage/".$this->shop_profile),
            'shop_key'=>$this->shop_key,
            "status"=>$this->status,
            "isSubscribe"=>$this->isSubscribe,
            "products_count"=>$this->products->count(),
            "expire"=>$this->expire,
            "subscribe_id"=>$this->subscribe_id,
            "status"=>$this->status,
            "images"=>$this->images
        ];
    }
}
