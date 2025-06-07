<?php

namespace App\Services\Shop;

use App\Models\Shop;
use App\Models\ShopVisit;
use Illuminate\Support\Facades\Request;

class CreateVisitShopService {

    public function visit($id,$ip,$userAgent){
        $shop=Shop::find($id);
        $ip =$ip;
        
        $userAgent = $userAgent;
        
         // Option : éviter de compter plusieurs fois par IP en 1h
        $alreadyVisited = ShopVisit::where('shop_id', $shop->id)
        ->where('ip', $ip)
        ->where('visited_at', '>=', now()->subHour())
        ->exists();

        if (! $alreadyVisited) {
            ShopVisit::create([
                'shop_id' => $shop->id,
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);
        }
    }
}