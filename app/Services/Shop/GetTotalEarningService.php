<?php

namespace App\Services\Shop;

use App\Models\Shop;
use App\Models\Order;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;

class GetTotalEarningService{

    public function getTotalEarning($shopId){
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