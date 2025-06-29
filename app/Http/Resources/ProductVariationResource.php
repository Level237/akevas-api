<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\ImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
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
            'product_id' => $this->product_id,
            'color' => [
                'id' => $this->color->id,
                'name' => $this->color->value,
                'hex' => $this->color->hex_color,
            ],
            'price' => $this->price,
            'quantity' => $this->quantity,
            'images' => ImageResource::collection($this->images),
            'attributes_variation' => $this->attributesVariation->map(function($attr) {
                return [
                    'id' => $attr->id,
                    'attribute_value' => [
                        'id' => $attr->attributeValue->id,
                        'value' => $attr->attributeValue->value,
                    ],
                    'quantity' => $attr->quantity,
                    'price' => $attr->price,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 