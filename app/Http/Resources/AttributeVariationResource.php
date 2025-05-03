<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeVariationResource extends JsonResource
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
           "name" => $this->value,
           "value" => $this ?? null,
           "quantity" => $this->quantity ?? null,
           "price" => $this->price ?? null,
        ];
    }
}
