<?php

namespace App\Http\Controllers\Gender;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use App\Http\Resources\GenderResource;

class CurrentGenderController extends Controller
{
    public function show($id)
    {
        $gender = Gender::find($id);
        if(!$gender){
            return response()->json(['message' => 'Gender not found'], 404);
        }
        return new GenderResource($gender);
    }

    public function all(){
        $genders = Gender::all();
        return $genders;
    }
}
