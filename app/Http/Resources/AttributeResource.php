<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            'name' => $this->attributes_name,
            'values' => $this->attributesValues,
            'groups' => $this->attributeValueGroups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'label' => $group->label,
                    'values' => $group->attributeValues,
                ];
            })
        ];
    }
}
