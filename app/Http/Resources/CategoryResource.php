<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "category_name"=>$this->category_name,
            "genders"=>$this->genders,
            "products_count"=>$this->products->count(),
            "category_profile"=>URL("/storage/".$this->category_profile),
            "category_url"=>$this->category_url,
            "parent"=>$this->parent,
        ];
    }
}
