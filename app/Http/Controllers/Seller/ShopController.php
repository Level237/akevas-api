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
        try{

            $shop=new Shop;
            $shop->shop_name=$request->shop_name;
            $shop->user_id=Auth::guard('api')->user()->id;
            $shop->shop_key=(new generateShopNameService())->generateShopName();
            $shop->shop_description=$request->shop_description;
            $shop->shop_type_id=$request->shop_type_id;
            $shop->shop_url=(new GenerateUrlResource())->generateUrl($request->shop_name);
            $file = $request->file('shop_profile');
            $image_path = $file->store('shops', 'public');
            $shop->shop_profile=$image_path;
            $shop->save();

            return response()->json(['message'=>"shop created successfully"],201);
        }catch(\Exception $e){
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
        $shop=Shop::find($id);

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

    public function getShopEarnings($shopId){
        $shop = Shop::findOrFail($shopId);
        $totalEarnings = 0.0;

        // Étape 1: Récupérer les IDs des produits de la boutique
        $productIds = $shop->products()->pluck('id');

        // Étape 2: Récupérer les IDs de commandes de produits simples
        $simpleOrderIds = DB::table('order_details')
            ->whereIn('product_id', $productIds)
            ->pluck('order_id');

        // Étape 3: Récupérer les IDs de commandes de produits variés
        $productVariationIds = ProductVariation::whereIn('product_id', $productIds)->pluck('id');

        $variedOrderIds = DB::table('order_variations')
            ->whereIn('product_variation_id', $productVariationIds)
            ->pluck('order_id');

        // Étape 4: Combiner les IDs de commandes et enlever les doublons
        $allOrderIds = $simpleOrderIds->merge($variedOrderIds)->unique();

        // Étape 5: Récupérer les commandes (payées et livrées) et calculer les gains
        $orders = Order::whereIn('id', $allOrderIds)
            ->get();
        
        foreach ($orders as $order) {
            $subtotal = floatval($order->total) - floatval($order->fee_of_shipping);
            
            // On retire la taxe de 5%
            $taxAmount = $subtotal * 0.05;
            $netEarnings = $subtotal - $taxAmount;
            
            $totalEarnings += $netEarnings;
        }

        return $totalEarnings;
    
    }
}
