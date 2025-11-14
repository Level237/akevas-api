<?php

namespace App\Http\Resources;

use App\Models\Town;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\ImageResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\WholeSalePriceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "product_name" => $this->product_name,
            "product_description" => $this->product_description,
            "isRejet"=>$this->isRejet,
            "shop_name" => $this->shop->shop_name,
            "user_id"=>$this->shop->user_id,
            "shop_key" => $this->shop->shop_key,
            "shop_created_at" => $this->shop->created_at,
            "product_gender" => $this->product_gender,
            "shop_profile" => URL("/storage/" . $this->shop->shop_profile),
            "reviewCount"=>$this->reviews->count(),
            "product_url" => $this->product_url,
            "is_only_wholesale"=>$this->is_only_wholesale,
            "whatsapp_number"=>$this->whatsapp_number,
            "isVariation" => $this->variations->isNotEmpty() ? true : null,
            "productWholeSales"=>WholeSalePriceResource::collection($this->wholesalePrices),
            "product_images" => ImageResource::collection($this->images),
            "product_profile" => URL("/storage/" . $this->product_profile),
            "product_price" => $this->product_price,
            "isWholeSale"=>$this->is_wholesale,
            "product_quantity" => $this->product_quantity,
            "parent_category" => CategoryResource::collection($this->categories->whereNull('parent_id')),
            "child_category" => CategoryResource::collection($this->categories->whereNotNull('parent_id')),
            "residence"=>Town::where('id',$this->product_residence)->select('town_name')->first()->town_name,
            "status" => $this->status,
            "isSubscribe" => $this->isSubscribe,
            "variations" => $this->getVariations(),
            "created_at" => $this->created_at,
            "subscribe_id" => $this->subscribe_id
        ];
    }
}
