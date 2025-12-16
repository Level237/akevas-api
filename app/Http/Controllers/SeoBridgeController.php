<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SeoBridgeController extends Controller
{
    public function __invoke(Request $request)
    {
        // L'URL envoyée par le .htaccess (ex: https://akevas.com/product/iphone-15)
        $fullUrl = $request->query('url');
        if (!$fullUrl) return abort(404);

        $api_url = "https://api-akevas.akevas.com";

        $host = parse_url($fullUrl, PHP_URL_HOST) ?? '';
        $path = parse_url($fullUrl, PHP_URL_PATH) ?? '/';

        Log::info($host);
        Log::info($path);
        // Valeurs par défaut (Si aucune règle ne correspond)
        $data = [
            'title' => "Akevas - Shopping & Business",
            'description' => "La plateforme de commerce de référence.",
            'image' => asset('home.png'),
            'url' => $fullUrl
        ];

        // --- 1. DOMAINE CLIENT (akevas.com) ---
        if ($host === 'akevas.com') {

            // Page Produit : akevas.com/product/slug
            if (Str::contains($path, '/produit/')) {
                $slug = Str::after($path, '/produit/');
                $product = Product::where('product_url', $slug)->first();
                if ($product) {
                    $data['title'] = $product->product_name . " | Akevas";
                    $data['description'] = Str::limit($product->product_description, 160);
                    $data['image'] = URL("/storage/" . $product->product_profile);
                }
            }

            // Page Boutique vue par le client : akevas.com/shop/slug
            elseif (Str::contains($path, '/shop/')) {
                $slug = Str::after($path, '/shop/');
                $shop = Shop::where('id', $slug)->first();
                if ($shop) {
                    $data['title'] = "Boutique " . $shop->shop_name . " | Akevas";
                    $data['description'] = $shop->shop_description;
                    $data['image'] = $shop->shop_profile;
                }
            }
        }

        // --- 2. DOMAINE VENDEUR (seller.akevas.com) ---
        elseif ($host === 'seller.akevas.com') {
            $data['title'] = "Espace Vendeur - Akevas";
            $data['description'] = "Gérez votre boutique et vos ventes sur Akevas.";
            $data['image'] = asset('images/meta-seller.png');
        }

        // --- 3. DOMAINE LIVREUR (delivery.akevas.com) ---
        elseif ($host === 'delivery.akevas.com') {
            $data['title'] = "Espace Livreur - Akevas";
            $data['description'] = "Gérez vos livraisons et vos revenus.";
            $data['image'] = asset('images/meta-delivery.png');
        }

        return view('seo_bridge', compact('data'));
    }
}
