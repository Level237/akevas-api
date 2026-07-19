<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GetAttributesController extends Controller
{
    public function getValue($id)
    {
        $cacheKey = "attributes.values.{$id}";
        $attributes = Cache::remember($cacheKey, now()->addHours(24), function () use ($id) {
            return Attribute::all();
        });

        // Retourner les attributs groupés
        return AttributeResource::collection($attributes);
    }
}
