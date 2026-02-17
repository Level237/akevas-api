<?php

namespace App\Http\Controllers\User;

use App\Models\Shop;
use App\Models\History;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Resources\ShopResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Log;
class SearchController extends Controller
{
    public function search($query, $userId)
    {

        $products = Product::where(function ($q) use ($query) {
            $q->where("product_name", 'like', "%{$query}%")
                ->orWhere("product_description", "like", "%{$query}%")
                ->orWhere("product_price", "like", "%{$query}%");
        })
            ->where('status', 1)
            ->take(5)
            ->get();

        $shops = Shop::where(function ($q) use ($query) {
            $q->where("shop_name", 'like', "%{$query}%")
                ->orWhere("shop_description", "like", "%{$query}%");
        })
            ->where('state', 1)
            ->take(5)
            ->get();

        if ($userId != 0) {
            $history = History::where('user_id', $userId)->where('search_term', $query)->first();
            if (isset($history)) {
                return;
            } else {
                $hystory = new History;
                $hystory->user_id = $userId;
                $hystory->search_term = $query;
                $hystory->save();
            }
        }

        return response()->json([
            'products' => ProductResource::collection($products),
            'shops' => ShopResource::collection($shops),
        ]);
    }

    public function recentHistory()
    {
        $user = Auth::guard('api')->user();

        if (isset($user)) {
            $histories = History::where('user_id', $user->id)->get();
            return response()->json($histories);
        }
        return response()->json([]);
    }
}
