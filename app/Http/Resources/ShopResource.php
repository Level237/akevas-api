<?php

namespace App\Http\Resources;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\ImageResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
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
            "products"=>ProductResource::collection($this->products),
            "expire"=>$this->expire,
            "subscribe_id"=>$this->subscribe_id,
            "town"=>$this->town->town_name,
            "quarter"=>$this->quarter->quarter_name,
            "isPublished"=>$this->isPublished,
            "categories"=>CategoryResource::collection($this->categories),
            
            "state"=>$this->state,
            "level"=>$this->shop_level,
            "cover"=>URL("/storage/".$this->shop_banner),
            "images"=>ImageResource::collection($this->images)
        ];
    }
}
