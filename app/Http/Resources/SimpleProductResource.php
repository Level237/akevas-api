<?php

namespace App\Http\Resources;

use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductResource extends JsonResource
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
            "product_profile" => URL("/storage/" . $this->product_profile),
            "product_price" => $this->product_price,
            "review_average"=>floatval($this->reviews->avg('rating')),
            "product_url"=>$this->product_url,
            "variations" => $this->getVariations(),
            "count_seller"=>OrderDetail::where("product_id",$this->id)->count(),
            "product_quantity" => $this->product_quantity,
        ];
    }
}
