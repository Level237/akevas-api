<?php

namespace App\Http\Resources;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\ImageResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
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
            'shop_id'=>$this->id,
            'shop_name'=>$this->shop_name,
            'shop_description'=>$this->shop_description,
            'shop_profile'=>URL("/storage/".$this->shop_profile),
            'shop_key'=>$this->shop_key,
            "review_average"=>floatval($this->reviews->avg('rating')),
            "reviewCount"=>$this->reviews->count(),
            "status"=>$this->status,
            "coins"=>$this->coins,
            "isSubscribe"=>$this->isSubscribe,
            "products_count"=>$this->products->count(),
            "products"=>ProductResource::collection($this->products),
            "expire"=>$this->expire,
            "subscribe_id"=>$this->subscribe_id,
            "town"=>$this->town->town_name,
            "quarter"=>$this->quarter->quarter_name,
            "isPublished"=>$this->isPublished,
            "visitTotal"=>$this->visits->count(),
            "categories"=>CategoryResource::collection($this->categories),
           "phone"=>$this->user->phone_number,
            "orders_count"=>$this->products->flatMap(function($product) {
                return $product->orderDetails->map(function($orderDetail) {
                    return $orderDetail->order;
                });
            })->unique('id')->values()->count(),
            "total_earnings"=>$this->products->flatMap(function($product) {
                return $product->orderDetails->map(function($orderDetail) {
                    return $orderDetail->unit_price * $orderDetail->order_product_quantity;
                });
            })->sum(),
            "state"=>$this->state,
            "level"=>$this->shop_level,
            "cover"=>URL("/storage/".$this->shop_banner),
            "images"=>ImageResource::collection($this->images)
        ];
    }
}
