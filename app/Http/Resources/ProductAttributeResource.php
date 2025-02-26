<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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
            'image' => URL("/storage/" . $this->pivot->image_path),
            'quantity' => $this->pivot->quantity,
            'price' => $this->pivot->price,
        ];
    }
}
