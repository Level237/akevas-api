<?php

namespace App\Http\Controllers\User;

use App\Models\Shop;
use App\Models\ShopReview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
class MakeReviewShopController extends Controller
{
    public function makeCommentShop($shop_id, Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }


        $shop = Shop::find($shop_id);
        if (!$shop) {
            return response()->json(['message' => 'Boutique introuvable'], 404);
        }

        // 2. Empêcher les avis multiples du même utilisateur
        $existingReview = ShopReview::where('shop_id', $shop_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'Vous avez déjà laissé un avis pour cette boutique.'], 400);
        }

        // 3. Création
        $review = ShopReview::create([
            'user_id' => $user->id,
            'shop_id' => $shop_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => 0,
        ]);

        // 4. 🚨 CRITIQUE : Invalider le cache
        Cache::forget("reviews.shop.{$shop_id}");

        return response()->json([
            'message' => 'Avis ajouté avec succès',
        ], 201);
    }
}
