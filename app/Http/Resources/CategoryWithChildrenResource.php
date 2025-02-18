<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryWithChildrenResource extends JsonResource
{
    protected $parentId;

    public function __construct($resource, $parentId = null)
    {
        parent::__construct($resource);
        $this->parentId = $parentId;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_name' => $this->category_name,
            'children' => $this->when($this->children->isNotEmpty(), function () {
                return $this->children
                    ->filter(function ($child) {
                        // Vérifie si l'enfant appartient aussi au parent de base
                        return $child->parent->contains('id', $this->parentId ?: $this->id);
                    })
                    ->map(function ($child) {
                        return new CategoryWithChildrenResource($child, $this->parentId ?: $this->id);
                    });
            })
        ];
    }
}
