<?php

namespace App\Http\Resources;

use App\Models\Shop;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\ShopResource;
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
            'shop'=>ShopResource::make(Shop::find(Product::find($this->product_id)->shop_id)),
            'product_name'=>$this->product->product_name,
            'color' => [
                'id' => $this->color->id,
                'name' => $this->color->value,
                'hex' => $this->color->hex_color,
            ],
            'price' => $this->price,
            'quantity' => $this->quantity,
            'images' => ImageResource::collection($this->images),
            'isWhole' => $this->product->wholesalePrices ? true : false,
            "wholeSalePrice" => $this->product->wholesalePrices,
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