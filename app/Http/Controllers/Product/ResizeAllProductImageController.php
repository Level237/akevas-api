<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ResizeProductImagesJob;

class ResizeAllProductImageController extends Controller
{
    public function resizeAllProductImage(){
        $products = Product::all();

        foreach ($products as $product) {
            dispatch(new ResizeProductImagesJob($product));
        }
    }
}
