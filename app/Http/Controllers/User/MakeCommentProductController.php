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
        try {
            $product = Product::find($product_id);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            $user = Auth::guard("api")->user();
            $review = Review::create([
                'user_id' => $user->id,
            'product_id' => $product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => 1,
            ]);
            return response()->json(['message' => 'Comment added successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
