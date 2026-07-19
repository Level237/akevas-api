<?php

namespace App\Http\Controllers\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Cache;
class CategoryByUrlController extends Controller
{
    public function index($url)
    {
        $cacheKey = 'category.url.' . $url;

        $category = Cache::remember($cacheKey, now()->addHours(6), function () use ($url) {
            return Category::where('category_url', $url)
                ->with(['parent', 'genders']) // Charge les relations en 1 seule requête
                ->withCount('products')       // ✅ Compte les produits en SQL (Zéro N+1 !)
                ->first();
        });

        if (!$category) {
            return response()->json(['message' => 'Catégorie introuvable.'], 404);
        }

        return response()->json(CategoryResource::make($category));
    }
}
