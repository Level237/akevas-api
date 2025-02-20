<?php

namespace App\Http\Controllers\Gender;

use App\Models\Gender;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetShopByGenderController extends Controller
{
    public function index($id)
    {
        $gender = Gender::find($id);
        if(!$gender){
            return response()->json(['message' => 'Gender not found'], 404);
        }
    }
}
