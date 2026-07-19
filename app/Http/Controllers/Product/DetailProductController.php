<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Cache;
class DetailProductController extends Controller
{
    public function index($product_url)
    {
        $cacheKey = "product.detail.{$product_url}";

        // 1. Cache de 30 minutes. La page produit est la plus visitée, il faut la protéger.
        $product = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($product_url) {
            return Product::where('product_url', $product_url)
                // 🚨 CRUCIAL : Charge TOUTES les relations utilisées dans ton ProductResource
                // Adapte cette liste selon les besoins réels de ton Resource
                ->with([
                    'shop',
                    'categories',
                    'wholesalePrices'
                ])
                ->first();
        });

        // 2. Gestion propre de l'erreur 404
        if (!$product) {
            return response()->json(['message' => 'Produit introuvable ou retiré.'], 404);
        }

        return ProductResource::make($product);
    }
}
