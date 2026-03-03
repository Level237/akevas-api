<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenderCategoryResource extends JsonResource
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
            'gender_name' => $this->gender_name,
            'gender_profile' => URL("/storage/" . $this->gender_profile),
            'categories' => CategoryResource::collection(Category::whereHas('genders', function ($query) {
                $query->where('gender_id', $this->id);
            })->where('parent_id', null)->get()),
        ];
    }
}
