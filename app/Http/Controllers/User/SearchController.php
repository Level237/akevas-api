<?php

namespace App\Http\Controllers\User;

use App\Models\Shop;
use App\Models\History;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Resources\ShopResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductResource;
class SearchController extends Controller
{
    public function search($query, $userId)
    {
        // 1. Nettoyage et validation de la requête
        $query = trim($query);

        // Si la requête est trop courte ou trop longue, retour vide
        if (strlen($query) < 2 || strlen($query) > 100) {
            return response()->json([
                'products' => [],
                'shops' => [],
            ]);
        }

        // 2. Création d'une clé de cache unique
        // On inclut userId=0 pour les recherches anonymes (cache partagé)
        $cacheKey = 'search.' . md5($query . '|' . $userId);

        // 3. Cache de 2 minutes (court car les recherches sont très dynamiques)
        $results = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($query) {

            // Recherche produits (limitée à 5 résultats)
            $products = Product::where(function ($q) use ($query) {
                $q->where("product_name", 'like', "%{$query}%")
                    ->orWhere("product_description", "like", "%{$query}%");
            })
                ->where('status', 1)
                ->take(5)
                ->get();

            // Recherche boutiques (limitée à 5 résultats)
            $shops = Shop::where(function ($q) use ($query) {
                $q->where("shop_name", 'like', "%{$query}%")
                    ->orWhere("shop_description", "like", "%{$query}%");
            })
                ->where('state', 1)
                ->take(5)
                ->get();

            return [
                'products' => ProductResource::collection($products),
                'shops' => ShopResource::collection($shops),
            ];
        });

        // 4. Enregistrement de l'historique (UNIQUEMENT si recherche réussie)
        if ($userId != 0 && $userId !== '0') {
            // On ne bloque PAS la recherche si l'historique existe déjà
            // On met juste à jour la date (optionnel) ou on ignore
            History::updateOrCreate(
                ['user_id' => $userId, 'search_term' => $query],
                ['updated_at' => now()]
            );
        }

        // 5. Retourner TOUJOURS une réponse valide
        return response()->json($results);
    }

    public function recentHistory()
    {
        $user = Auth::guard('api')->user();

        if (isset($user)) {
            // Limite à 10 recherches récentes + tri par date décroissante
            $histories = History::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            return response()->json($histories);
        }
        return response()->json([]);
    }
}
