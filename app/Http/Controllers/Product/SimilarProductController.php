<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SimpleProductResource;

class SimilarProductController extends Controller
{
    public function getSimilarProducts($id){
        $referenceCategories=Product::find($id)->categories()->pluck('categories.id');

        $similarProducts=Product::whereHas('categories',function($query) use 
        ($referenceCategories){
            $query->whereIn('categories.id',$referenceCategories);
        })->where('id','!=',$id)->get();

        return SimpleProductResource::collection($similarProducts);
        
    }
}
