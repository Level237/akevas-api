<?php

namespace App\Http\Controllers\User;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
class MakeCommentProductController extends Controller
{
    public function makeCommentProduct($product_id,Request $request)
    {
        $product = Product::find($product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $user = Auth::guard("api")->user()->id;
        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        return response()->json(['message' => 'Comment added successfully'], 200);
    }
}
