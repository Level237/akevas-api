<?php

namespace App\Http\Controllers\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
class CategoryByUrlController extends Controller
{
    public function index($url){
        $category=Category::where('category_url',$url)->first();
        return response()->json(CategoryResource::make($category));
    }
}
