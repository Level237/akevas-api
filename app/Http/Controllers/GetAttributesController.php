<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

class GetAttributesController extends Controller
{
    public function getValue($id)
    {

        $attributes = Attribute::all();

        // Retourner les attributs groupés
        return AttributeResource::collection($attributes);
    }
}
