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
use App\Models\ProductAttributesValue;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop = Shop::where('user_id', Auth::guard('api')->user()->id)->first();
        $products = Product::where('shop_id', $shop->id)->orderBy('created_at', 'desc')->get();

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $product = new Product;
            $user = Auth::guard('api')->user();
            $shop = Shop::where('user_id', $user->id)->first();
            if ($shop->products()->count() === 0) {
                $shop->shop_level = "3";
                $shop->save();
            }

            $product->product_name = $request->product_name;
            $product->product_url = (new GenerateUrlResource())->generateUrl($request->product_name);
            $product->product_description = $request->product_description;
            $product->shop_id = $shop->id;
            $product->product_price = $request->product_price;
            $product->product_quantity = $request->product_quantity;
            $product->product_gender = (string)$request->product_gender;
            $product_profile = $request->file('product_profile');
            $product->status = 1;
            $product->product_profile = $product_profile->store('product/profile', 'public');
            $product->whatsapp_number = $request->whatsapp_number;
            $product->product_residence = $request->product_residence;
            if ($product->save()) {
                if ($request->hasFile('images')) {
                    $images = $request->file('images');

                    foreach ($images as $image) {
                        $i = new Image;
                        $i->image_path = $image->store('product/images', 'public');
                        if ($i->save()) {
                            $product->images()->attach($i);
                        }
                    }
                    if ($request->filled('variants')) {
                        try {
                            DB::beginTransaction(); // Début de la transaction
                    
                            $variantNameMap = [];
                            $allAttributesData = [];
                            $variants = json_decode($request->variants, true); // Conversion en array PHP
                    
                            foreach ($variants as $index => $variant) {
                                // Validation du nom de variante unique
                                if (isset($variantNameMap[$variant['variant_name']])) {
                                    throw new \Exception('Duplicate variant name found: ' . $variant['variant_name']);
                                }
                                $variantNameMap[$variant['variant_name']] = true;
                    
                                // Traitement des attributs
                                if (!empty($variant['attribute_value_id'])) {
                                    $image_paths = [];
                                    
                                    // Vérification et traitement des images
                                    $variantImageKey = "variant_images_{$index}_0"; // Nouveau format de clé
                                    if ($request->hasFile($variantImageKey)) {
                                        // Traitement de toutes les images pour cette variante
                                        $imageIndex = 0;
                                        while ($request->hasFile("variant_images_{$index}_{$imageIndex}")) {
                                            $image = $request->file("variant_images_{$index}_{$imageIndex}");
                                            $image_paths[] = $image->store("product/variants", "public");
                                            $imageIndex++;
                                        }
                                    }
                    
                                    // Stockage des données de la variante
                                    foreach ($variant['attribute_value_id'] as $attributeId) {
                                        $allAttributesData[] = [
                                            'attribute_id' => $attributeId,
                                            'price' => (string)$variant['price'],
                                            'image_paths' => $image_paths,
                                            'variant_name' => $variant['variant_name']
                                        ];
                                    }
                                }
                            }
                    
                            // Suppression des anciennes relations
                            $product->attributes()->detach();
                    
                            // Création des nouvelles relations
                            foreach ($allAttributesData as $data) {
                                // Création de la relation produit-attribut
                                $product->attributes()->attach($data['attribute_id'], [
                                    'price' => $data['price'],
                                    'variant_name' => $data['variant_name']
                                ]);
                    
                                // Création du ProductAttributesValue
                                $productAttributeValue = ProductAttributesValue::create([
                                    'product_id' => $product->id,
                                    'attributes_id' => $data['attribute_id'],
                                    'price' => $data['price'],
                                    'variant_name' => $data['variant_name']
                                ]);
                    
                                // Création et association des images
                                foreach ($data['image_paths'] as $image_path) {
                                    $image = Image::create(['path' => $image_path]);
                                    $productAttributeValue->images()->attach($image->id);
                                }
                            }
                    
                            DB::commit(); // Validation de la transaction
                    
                            return response()->json([
                                'success' => true,
                                'message' => 'Product variants created successfully'
                            ]);
                    
                        } catch (\Exception $e) {
                            DB::rollBack(); // Annulation en cas d'erreur
                            
                            return response()->json([
                                'success' => false,
                                'message' => 'Error creating variants: ' . $e->getMessage()
                            ], 422);
                        }
                    }

                    if ($request->has('categories') && is_array($request->categories)) {
                        $product->categories()->attach(array_map('intval', $request->categories));
                    }
                    if ($request->has('sub_categories') && is_array($request->sub_categories)) {
                        $product->categories()->attach(array_map('intval', $request->sub_categories));
                    }
                }
            }


            return response()->json(['message' => "Product created successfully"], 201);
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
