<?php

namespace App\Http\Controllers\User;

use App\Models\Shop;
use App\Models\ShopReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MakeReviewShopController extends Controller
{
    public function makeCommentShop($shop_id,Request $request)
    {
        try {
            $shop = Shop::find($shop_id);
            if (!$shop) {
                return response()->json(['message' => 'Shop not found'], 404);
            }
            $user = Auth::guard("api")->user();
            $review = ShopReview::create([
            'user_id' => $user->id,
            'shop_id' => $shop_id,
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
