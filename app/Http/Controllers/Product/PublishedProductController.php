<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublishedProductController extends Controller
{
    public function publishedProduct($id)
    {
        $product = Product::find($id);
        if($product->status == 0){
            $product->status = 1;
            $product->save();
            return response()->json(['message' => 'Product published successfully']);
        }else{
            $product->status = 0;
            $product->save();
            return response()->json(['message' => 'Product unpublished successfully']);
        }
    }
}
