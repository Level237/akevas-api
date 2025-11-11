<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use App\Models\FeedBack;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublishedProductController extends Controller
{
    public function publishedProduct($id,Request $request)
    {
        $product = Product::find($id);
        if($product->status == 0){
            $product->status = 1;
            $product->isRejet=0;
            $product->save();
            return response()->json(['message' => 'Product published successfully']);
        }else{
            if($request->message){
                $feedBack=new FeedBack;
                $feedBack->user_id=$request->user_id;
                $feedBack->message=$request->message;
                $feedBack->status=0;
                $feedBack->type="1";
                $feedBack->product_id=$product->id;
                $product->isRejet=1;
                $feedBack->save();
            }
            $product->status = 0;
            $product->save();
            return response()->json(['message' => 'Product unpublished successfully']);
        }
    }
}
