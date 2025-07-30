<?php

namespace App\Http\Resources;

use App\Models\Town;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SimpleProductResource;
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
            "id" => $this->id,
            "product_name" => $this->product_name,
            "product_description" => $this->product_description,
            "shop_name" => $this->shop->shop_name,
            "shop_key" => $this->shop->shop_key,
            "shop_created_at" => $this->shop->created_at,
            "shop_profile" => URL("/storage/" . $this->shop->shop_profile),
            "shop_id" => $this->shop->id,
            "review_average"=>floatval($this->reviews->avg('rating')),
            "count_seller"=>OrderDetail::where("product_id",$this->id)->count(),
            "reviewCount"=>$this->reviews->count(),
            "product_url" => $this->product_url,
            "whatsapp_number"=>$this->whatsapp_number,
            "product_images" => ImageResource::collection($this->images),
            "product_profile" => URL("/storage/" . $this->product_profile),
            "product_price" => $this->product_price,
            "product_quantity" => $this->product_quantity,
            "product_categories" => CategoryResource::collection($this->categories),
            "residence"=>Town::where('id',$this->product_residence)->select('town_name')->first()->town_name,
            "status" => $this->status,
            "isSubscribe" => $this->isSubscribe,
            "variations" => $this->getVariations(),
            "expire" => $this->expire,
            "reviews" => ReviewResource::collection($this->reviews),
            "created_at" => $this->created_at,
            "subscribe_id" => $this->subscribe_id
        ];
    }
}
