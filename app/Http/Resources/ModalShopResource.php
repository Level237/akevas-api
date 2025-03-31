<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ModalShopResource extends JsonResource
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
            'shop_name'=>$this->shop_name,
            'shop_description'=>$this->shop_description,
            'shop_profile'=>URL("/storage/".$this->shop_profile),
            'shop_key'=>$this->shop_key,
            "review_average"=>floatval($this->reviews->avg('rating')),
            "reviewCount"=>$this->reviews->count(),
            "status"=>$this->status,
            "isSubscribe"=>$this->isSubscribe,
            "products_count"=>$this->products->count(),
            "town"=>$this->town->town_name,
            "quarter"=>$this->quarter->quarter_name,
            "cover"=>ImageResource::make($this->images[0]),
            ""
        ];
}
}