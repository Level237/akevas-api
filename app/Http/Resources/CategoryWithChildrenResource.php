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
                // D'abord, identifions les enfants qui ont leurs propres enfants
                $childrenWithChildren = $this->children->filter(function ($child) {
                    return $child->parent->contains('id', $this->parentId ?: $this->id)
                        && $child->children->isNotEmpty();
                });

                // Récupérons tous les IDs des sous-catégories de nos enfants
                $grandChildrenIds = $childrenWithChildren->flatMap(function ($child) {
                    return $child->children->pluck('id');
                })->unique();

                // Filtrons les enfants directs
                $directChildren = $this->children->filter(function ($child) use ($grandChildrenIds) {
                    return $child->parent->contains('id', $this->parentId ?: $this->id)
                        && !$grandChildrenIds->contains($child->id);
                });

                // Fusionnons et mappons le résultat
                return $childrenWithChildren->merge($directChildren)
                    ->map(function ($child) {
                        return new CategoryWithChildrenResource($child, $this->parentId ?: $this->id);
                    });
            })
        ];
    }
}
