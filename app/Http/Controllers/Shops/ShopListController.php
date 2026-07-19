<?php

namespace App\Http\Controllers\Shops;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopListController extends Controller
{
    public function index()
    {
        $cacheKey = 'shops.home.featured';

        // 🚨 Le mélange aléatoire n'est calculé que TOUTES LES 2 HEURES.
        // Le reste du temps, c'est servi instantanément depuis le cache (0 requête SQL).
        $shops = Cache::remember($cacheKey, now()->addHours(2), function () {
            return Shop::where('state', 1)
                //->with(['user:id,name,profile']) // 🚨 CRUCIAL : Évite le N+1 si ton Resource affiche le vendeur
                ->inRandomOrder()
                ->take(7)
                ->get();
        });

        return ShopResource::collection($shops);
    }

    public function all(Request $request)
    {
        $page = $request->get('page', 1);
        $cacheKey = "shops.all.page.{$page}";

        // Cache de 15 minutes pour la pagination. 
        // Si un utilisateur actualise la page 2, il ne recharge pas la DB.
        $shops = Cache::remember($cacheKey, now()->addMinutes(15), function () {
            return Shop::where('state', 1)
                //->with(['user:id,name,profile', 'town:id,town_name']) // 🚨 Évite le N+1
                ->orderBy('created_at', 'desc')
                ->paginate(6);
        });

        return ShopResource::collection($shops);
    }

    public function adsShops($id)
    {
        $cacheKey = "shops.ads.subscribe.{$id}";

        $shops = Cache::remember($cacheKey, now()->addHours(2), function () use ($id) {
            return Shop::where('state', 1)
                ->where('subscribe_id', $id)
                ->where('status', 1) // Conservé selon ton code original
                //->with(['user:id,name,profile']) // 🚨 Évite le N+1
                ->inRandomOrder()
                ->get();
        });

        return ShopResource::collection($shops);
    }
}