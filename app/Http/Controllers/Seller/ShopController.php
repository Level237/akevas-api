<?php

namespace App\Http\Controllers\Seller;

use App\Models\Shop;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ProductVariation;
use App\Http\Requests\ShopRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\GenerateUrlResource;
use App\Service\Shop\generateShopNameService;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopRequest $request)
    {
        try {

            $shop = new Shop;
            $shop->shop_name = $request->shop_name;
            $shop->user_id = Auth::guard('api')->user()->id;
            $shop->shop_key = (new generateShopNameService())->generateShopName();
            $shop->shop_description = $request->shop_description;
            $shop->shop_type_id = $request->shop_type_id;
            $shop->shop_url = (new GenerateUrlResource())->generateUrl($request->shop_name);
            $file = $request->file('shop_profile');
            $image_path = $file->store('shops', 'public');
            $shop->shop_profile = $image_path;
            $shop->save();

            return response()->json(['message' => "shop created successfully"], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e
            ], 500);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop = Shop::find($id);

        return $shop;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getShopEarnings($shopId)
    {
        $cacheKey = "shop.earnings.{$shopId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($shopId) {

            // 1. Récupérer UNIQUEMENT les IDs des commandes (pas les modèles complets)
            // On utilise des JOINs pour éviter de charger tous les produits en mémoire
            $simpleOrderIds = DB::table('order_details')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->where('products.shop_id', $shopId)
                ->pluck('order_details.order_id');

            $variedOrderIds = DB::table('order_variations')
                ->join('product_variations', 'order_variations.product_variation_id', '=', 'product_variations.id')
                ->join('products', 'product_variations.product_id', '=', 'products.id')
                ->where('products.shop_id', $shopId)
                ->pluck('order_variations.order_id');

            $allOrderIds = $simpleOrderIds->merge($variedOrderIds)->unique();

            if ($allOrderIds->isEmpty()) {
                return 0.0;
            }

            // 2. 🚨 CRITIQUE : LAISSER LA BASE DE DONNÉES FAIRE LE CALCUL (SUM)
            // Au lieu de charger les commandes en PHP et de boucler, on demande à MySQL de faire la somme.
            // C'est des milliers de fois plus rapide et ça ne consomme aucune mémoire PHP.
            $totalEarnings = DB::table('orders')
                ->whereIn('id', $allOrderIds)
                // 🚨 ADAPTE 'delivered' ou 'paid' selon le nom réel de ta colonne de statut
                ->where('status', 'delivered')
                ->select(DB::raw('SUM((total - fee_of_shipping) * 0.95) as total_earnings'))
                ->value('total_earnings');

            return (float) ($totalEarnings ?? 0);
        });

    }

    public function countShopSales($shopId)
    {
        $cacheKey = "shop.sales_count.{$shopId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($shopId) {

            // 1. Récupérer UNIQUEMENT les IDs des commandes via des JOINs
            $simpleOrderIds = DB::table('order_details')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->where('products.shop_id', $shopId)
                ->pluck('order_details.order_id');

            $variedOrderIds = DB::table('order_variations')
                ->join('product_variations', 'order_variations.product_variation_id', '=', 'product_variations.id')
                ->join('products', 'product_variations.product_id', '=', 'products.id')
                ->where('products.shop_id', $shopId)
                ->pluck('order_variations.order_id');

            $allOrderIds = $simpleOrderIds->merge($variedOrderIds)->unique();

            if ($allOrderIds->isEmpty()) {
                return 0;
            }

            // 2. 🚨 CRITIQUE : LAISSER LA BASE DE DONNÉES COMPTER (COUNT)
            return DB::table('orders')
                ->whereIn('id', $allOrderIds)
                // 🚨 ADAPTE 'delivered' ou 'paid' selon le nom réel de ta colonne de statut
                ->where('status', 'delivered')
                ->count();
        });
    }
}
