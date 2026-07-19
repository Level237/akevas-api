<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "category_name" => $this->category_name,

            // ✅ Utilise whenLoaded pour éviter les requêtes si la relation n'est pas chargée
            "genders" => $this->whenLoaded('genders', function () {
                return $this->genders->map(function ($gender) {
                    return [
                        'id' => $gender->id,
                        'name' => $gender->name // Adapte selon ton modèle Gender
                    ];
                });
            }),

            // ✅ CORRECTION CRITIQUE : Utilise le withCount du contrôleur, PAS ->count()
            "products_count" => $this->products_count ?? 0,

            "category_profile" => $this->category_profile ? URL("/storage/" . $this->category_profile) : null,
            "category_url" => $this->category_url,

            // ✅ Utilise whenLoaded ou parent_id directement
            "parent" => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'name' => $this->parent->category_name
                ];
            }),
        ];
    }
}