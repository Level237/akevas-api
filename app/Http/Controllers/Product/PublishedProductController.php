<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use App\Models\FeedBack;
use Illuminate\Http\Request;
use App\Jobs\SendDeclineProductJob;
use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationSellerJob;

class PublishedProductController extends Controller
{
    public function publishedProduct($id,Request $request)
    {
        $product = Product::find($id);
        if($request->message && $request->user_id){

            $feedBack=new FeedBack;
                $feedBack->user_id=$request->user_id;
                $feedBack->message=$request->message;
                $feedBack->status=0;
                $feedBack->type="1";
                $feedBack->product_id=$product->id;
                $product->isRejet=1;
                $feedBack->save();
                SendDeclineProductJob::dispatch($product,$request->message);
            
            $product->status = 0;
            $product->save();
            return response()->json(['message' => 'Product unpublished successfully']);
        }else{
            $product->status = 1;
            $product->isRejet=0;
            $product->save();
            return response()->json(['message' => 'Product published successfully']);
        }
            
    }
}
