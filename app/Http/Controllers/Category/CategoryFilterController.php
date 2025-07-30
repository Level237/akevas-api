<?php

namespace App\Http\Controllers\Category;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;

class CategoryFilterController extends Controller
{
    public function filter($arrayId){
        $array=trim($arrayId,"[]");
        $items = explode(',', $array);
        
        return ProductResource::collection(Product::with('categories')->whereHas('categories',function($query) use ($items){
            $query->whereIn('categories.id',$items);
        })->orderBy('created_at', 'desc')->paginate(6));
    }

    public function getCategoryBySubCategory($arraySubCategoryId){
        $array=trim($arraySubCategoryId,"[]");
        $items = explode(',', $array);
        $categories = collect();
        $currentCategories = Category::whereIn('id', $items)->get();
        
        foreach ($currentCategories as $category) {
            $current = $category;
            while ($current->parent_id !== null) {
                $current = Category::find($current->parent_id);
            }
            $categories->push($current);
        }
        
        $category = $categories->unique('id');
        return $category;
    }
}
