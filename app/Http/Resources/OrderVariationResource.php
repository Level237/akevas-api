<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ProductAttributeResource;
use App\Http\Resources\ProductVariationResource;
use Illuminate\Http\Resources\Json\JsonResource;


class OrderVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'variation_quantity' => $this->variation_quantity,
            'variation_price' => $this->variation_price,
            'product_variation' => $this->product_variation_id ? ProductVariationResource::make($this->productVariation) : null,
            'variation_attribute' => $this->variation_attribute_id ? ProductAttributeResource::make($this->variationAttribute) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 