<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryWithChildrenResource;

class ListCategoryController extends Controller
{
    public function index($parentId)
    {
        $parentCategory = Category::with('children')->findOrFail($parentId);
        
        return new CategoryWithChildrenResource($parentCategory);
    }
    public function getCategoryWithParentIdNull(){
        $rootCategories = Category::whereDoesntHave('parent')->get();

        return response()->json(['categories'=>$rootCategories],200);
    }
}
