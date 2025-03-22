<?php

namespace App\Http\Resources;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\ImageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeResource extends JsonResource
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
            'variant_name' => $this->pivot->variant_name,
            'images' => $this->productAttributesValues->map(function ($attribute) {
                return DB::table('product_attributes_value_image')->where('attributes_id', $attribute->id)->get()->map(function ($image) {
                    return ImageResource::make(Image::find($image->image_id));
                });
            }),
            'quantity' => $this->pivot->quantity,
            'price' => $this->pivot->price,
        ];
    }
}
