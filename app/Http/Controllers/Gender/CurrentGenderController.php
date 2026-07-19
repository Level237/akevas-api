<?php

namespace App\Http\Controllers\Gender;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenderCategoryResource;
use App\Models\Gender;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\GenderResource;

class CurrentGenderController extends Controller
{
    public function show($id)
    {
        $cacheKey = "gender.full.{$id}";

        $gender = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($id) {
            return Gender::find($id);
        });

        if (!$gender) {
            return response()->json(['message' => 'Gender not found'], 404);
        }

        return new GenderResource($gender);
    }

    public function GenderWithCategories($id)
    {
        $cacheKey = "gender.categories.{$id}";

        $gender = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($id) {
            return Gender::find($id);
        });

        if (!$gender) {
            return response()->json(['message' => 'Gender not found'], 404);
        }

        return new GenderCategoryResource($gender);
    }

    public function all()
    {
        $cacheKey = 'genders.all';

        $genders = Cache::remember($cacheKey, now()->addHours(24), function () {
            return Gender::all();
        });

        return $genders;
    }
}
