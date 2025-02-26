<?php

namespace App\Http\Controllers\Seller;

use App\Models\Shop;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\GenerateUrlResource;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop = Shop::where('user_id', Auth::guard('api')->user()->id)->first();
        $products = Product::where('shop_id', $shop->id)->get();

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $product = Product::find("01269ba7-f88d-4535-b685-465c8c3b340e");
            $variantNameMap = [];
            $allAttributesData = [];

            foreach (json_decode($request->variants) as $index => $variant) {
                // Vérifier si le variant_name existe déjà
                if (isset($variantNameMap[$variant->variant_name])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Duplicate variant name found: ' . $variant->variant_name
                    ], 422);
                }

                $image_path = $request->variant_images[$index]->store("product/variants", "public");

                // Stocker la première occurrence de chaque variant_name
                $variantNameMap[$variant->variant_name] = true;

                // Pour chaque variant, on ne prend que le premier attribute_value_id
                if (!empty($variant->attribute_value_id)) {
                    $attribute = $variant->attribute_value_id[0]; // Prendre seulement le premier attribut
                    $allAttributesData[] = [
                        'attribute_id' => $attribute,
                        'quantity' => (string)$variant->stock,
                        'price' => (string)$variant->price,
                        'image_path' => $image_path,
                        'variant_name' => $variant->variant_name
                    ];
                }
            }

            // Supprimer d'abord toutes les anciennes relations
            $product->attributes()->detach();

            // Attacher les nouvelles relations
            foreach ($allAttributesData as $data) {
                $product->attributes()->attach($data['attribute_id'], [
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                    'image_path' => $data['image_path'],
                    'variant_name' => $data['variant_name']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product variants saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
}
