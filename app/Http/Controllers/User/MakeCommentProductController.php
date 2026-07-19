<?php

namespace App\Http\Controllers\User;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
class MakeCommentProductController extends Controller
{
    public function makeCommentProduct($product_id, Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }



        $product = Product::find($product_id);
        if (!$product) {
            return response()->json(['message' => 'Produit introuvable'], 404);
        }

        // 2. Empêcher un utilisateur de laisser 50 avis sur le même produit
        $existingReview = Review::where('product_id', $product_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'Vous avez déjà laissé un avis pour ce produit.'], 400);
        }

        // 3. Création de l'avis
        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => 0,
        ]);
        Cache::forget("reviews.product.{$product_id}");
        return response()->json([
            'message' => 'Avis ajouté avec succès',
        ], 201);

    }
}
