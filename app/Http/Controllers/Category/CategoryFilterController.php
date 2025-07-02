<?php

namespace App\Http\Controllers\Category;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class CategoryFilterController extends Controller
{
    public function filter($arrayId){
        $array=trim($arrayId,"[]");
        $items = explode(',', $array);
        
        return ProductResource::collection(Product::with('categories')->whereHas('categories',function($query) use ($categoryUrl){
            $query->whereIn('categories.id',$items);
        })->orderBy('created_at', 'desc')->paginate(6));
    }
}
