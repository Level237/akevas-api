<?php

namespace App\Http\Controllers\Seller;

use Exception;
use App\Models\Shop;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\WholeSalePrice;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateProductUrlJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\GenerateUrlResource;
use App\Models\ProductAttributesValue;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductEditResource;

    class ProductController extends Controller
    {
        /**
         * Display a listing of the resource.
         */
        public function index()
        {
            $shop = Shop::where('user_id', Auth::guard('api')->user()->id)->first();
            $products = Product::where('shop_id', $shop->id)->orderBy('created_at', 'desc')
            ->where("is_trashed",0)
            ->get();

            return ProductResource::collection($products);
        }

        public function productListOfTrash(){
            $shop = Shop::where('user_id', Auth::guard('api')->user()->id)->first();
            $products = Product::where('shop_id', $shop->id)->orderBy('created_at', 'desc')
            ->where("is_trashed",1)
            ->get();

            return ProductResource::collection($products);
        }

       public function productListOfRejected(){
            $shop = Shop::where('user_id', Auth::guard('api')->user()->id)->first();
            $products=Product::where('shop_id',$shop->id)->orderBy("created_at","desc")
            ->where("isRejet",1)
            ->get();

            return ProductResource::collection($products);
       }
   public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Log de la requête entrante pour le débogage
            Log::info('Product store request received', ['request_all' => $request->all()]);
            Log::info('Files in request', ['request_files' => $request->files->all()]);

            $user = Auth::guard('api')->user();
            $shop = Shop::where('user_id', $user->id)->first();

            if (!$shop) {
                throw new \Exception("Shop not found for the authenticated user.");
            }

            // Mise à jour du niveau du shop si c'est le premier produit
            if ($shop->products()->count() === 0) {
                $shop->shop_level = "3";
                $shop->save();
            }

            $product = new Product;
            $product->product_name = $request->product_name;
            $product->product_description = $request->product_description;
            $product->shop_id = $shop->id;
            $product->type = $request->type == 'simple' ? 0 : 1; // 0 pour simple, 1 pour variable
            $product->product_gender = $request->product_gender;
            $product->whatsapp_number = $request->whatsapp_number;
            $product->product_residence = $request->product_residence;
            $product->status = 0; // Statut par défaut

            // Gestion du produit simple
            if ($product->type == 0) {
                $product->product_price = $request->product_price;
                $product->product_quantity = $request->product_quantity;
            }

            // Gestion de l'image principale
            if ($request->hasFile('product_profile')) {
                $product->product_profile = $request->file('product_profile')->store('product/profile', 'public');
                Log::info('Product profile image stored', ['path' => $product->product_profile]);
            }

            $product->save(); // Sauvegarde du produit pour obtenir l'ID
            Log::info('Product saved', ['product_id' => $product->id]);

            GenerateProductUrlJob::dispatch($product->id);

            // Gestion des prix de gros au niveau du produit (pour les produits simples ou les variations 'couleur uniquement' avec prix de gros global)
            if ($request->is_wholesale == "1") {
                $product->is_wholesale = true;
                if ($request->is_only_wholesale == "1") {
                    $product->is_only_wholesale = true;
                }
                $product->save(); // Sauvegarde pour mettre à jour is_wholesale et is_only_wholesale

                if ($request->has('wholesale_prices')) {
                    $wholesalePricesData = json_decode($request->wholesale_prices, true);
                    if ($wholesalePricesData) {
                        foreach ($wholesalePricesData as $wpData) {
                            // Lie les prix de gros au produit directement via la relation polymorphique

                            if($wpData['wholesale_price'] != "0"){
                                $product->wholesalePrices()->create([
                                    'min_quantity' => $wpData['min_quantity'],
                                    'wholesale_price' => $wpData['wholesale_price'],
                                ]);
                            }
                           
                            Log::info('Product global wholesale price saved', ['product_id' => $product->id, 'min_quantity' => $wpData['min_quantity']]);
                        }
                    }
                }
            }

            // Gestion des images du produit simple (non liées aux variations)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('product/images', 'public');
                    $product->images()->create(['image_path' => $imagePath]);
                    Log::info('Product main image stored', ['path' => $imagePath]);
                }
            }

            // Gestion des variations pour produit variable
            if ($product->type == 1 && $request->filled('variations')) {
                $variationsData = json_decode($request->variations, true);
                Log::info('Decoded variations data', ['variations' => $variationsData]);

                foreach ($variationsData as $colorGroup) {
                    // Vérifie si c'est une variation "couleur uniquement" ou "couleur et attribut"
                    // En se basant sur la présence des tableaux 'sizes' ou 'shoeSizes'
                    $isColorAndAttribute = (isset($colorGroup['sizes']) && is_array($colorGroup['sizes']) && count($colorGroup['sizes']) > 0) ||
                                           (isset($colorGroup['shoeSizes']) && is_array($colorGroup['shoeSizes']) && count($colorGroup['shoeSizes']) > 0);

                    // Création de la variation principale (couleur)
                    $variation = $product->variations()->create([
                        'color_id' => $colorGroup['color']['id'],
                        // Pour 'Couleur uniquement', le prix et la quantité sont directement sur la variation de couleur
                        'price' => !$isColorAndAttribute && isset($colorGroup['price']) ? $colorGroup['price'] : 0,
                        'quantity' => !$isColorAndAttribute && isset($colorGroup['quantity']) ? $colorGroup['quantity'] : null,
                    ]);
                    Log::info('ProductVariation (color) created', ['variation_id' => $variation->id, 'color_id' => $colorGroup['color']['id']]);

                    // Gestion des images pour cette couleur (envoyées sous 'color_{id}_image_{index}')
                    $colorImageKeyPrefix = "color_" . $colorGroup['color']['id'] . "_image_";
                    $imageIndex = 0;
                    while ($request->hasFile($colorImageKeyPrefix . $imageIndex)) {
                        $imageFile = $request->file($colorImageKeyPrefix . $imageIndex);
                        $imagePath = $imageFile->store('product/variations', 'public');
                        $variation->images()->create(['image_path' => $imagePath]); // Assurez-vous que ProductVariation a une relation images() morphMany
                        Log::info('Variation image stored', ['color_id' => $colorGroup['color']['id'], 'image_index' => $imageIndex, 'path' => $imagePath]);
                        $imageIndex++;
                    }

                    // Gestion des sous-variations (tailles/pointures/autres attributs)
                    if ($isColorAndAttribute) {
                        // Traitement des tailles
                        if (isset($colorGroup['sizes']) && is_array($colorGroup['sizes'])) {
                            Log::info('Processing sizes for color group', ['color_id' => $colorGroup['color']['id'], 'sizes_data' => $colorGroup['sizes']]);
                            foreach ($colorGroup['sizes'] as $attributeValue) {
                                // Crée ou met à jour la VariationAttribute
                                $attrVariation = $variation->attributesVariation()->updateOrCreate(
                                    ['attribute_value_id' => $attributeValue['id']],
                                    [
                                        'quantity' => $attributeValue['quantity'],
                                        'price' => $attributeValue['price'],
                                    ]
                                );
                                Log::info('VariationAttribute (size) processed', ['id' => $attrVariation->id, 'attribute_value_id' => $attributeValue['id']]);

                                // Gestion des prix de gros pour cette VariationAttribute spécifique
                              
                            }
                        }

                        // Traitement des pointures
                        if (isset($colorGroup['shoeSizes']) && is_array($colorGroup['shoeSizes'])) {
                            Log::info('Processing shoe sizes for color group', ['color_id' => $colorGroup['color']['id'], 'shoe_sizes_data' => $colorGroup['shoeSizes']]);
                            foreach ($colorGroup['shoeSizes'] as $attributeValue) {
                                // Crée ou met à jour la VariationAttribute
                                $attrVariation = $variation->attributesVariation()->updateOrCreate(
                                    ['attribute_value_id' => $attributeValue['id']],
                                    [
                                        'quantity' => $attributeValue['quantity'],
                                        'price' => $attributeValue['price'],
                                    ]
                                );
                                Log::info('VariationAttribute (shoe size) processed', ['id' => $attrVariation->id, 'attribute_value_id' => $attributeValue['id']]);

                                // Gestion des prix de gros pour cette VariationAttribute spécifique
                               
                            }
                        }
                    }
                }
            }

            // Gestion des catégories et sous-catégories
            if ($request->has('categories') && is_array($request->categories)) {
                $product->categories()->attach(array_map('intval', $request->categories));
                Log::info('Categories attached', ['categories' => $request->categories]);
            }

            if ($request->has('sub_categories') && is_array($request->sub_categories)) {
                $product->categories()->attach(array_map('intval', $request->sub_categories));
                Log::info('Sub-categories attached', ['sub_categories' => $request->sub_categories]);
            }

            DB::commit();
            Log::info('Product creation transaction committed successfully for product', ['product_id' => $product->id]);

            return response()->json(['message' => "Product created successfully"], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_all' => $request->all(), // Log toutes les données de la requête en cas d'échec
                'request_files' => $request->files->all(), // Log tous les fichiers en cas d'échec
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
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

        public function putInTrash($id){
            $product=Product::find($id);
            $product->is_trashed=1;
            
            $product->save();
            return response()->json(['message' => 'Product put in trash successfully']);
        }

        public function restoreProduct($id){
            $product=Product::find($id);
            $product->is_trashed=0;
            $product->save();
            return response()->json(['message' => 'Product restore successfully']);
        }

        public function getEditProduct($url){
            $product=Product::where('product_url',$url)->first();
            return response()->json(new ProductEditResource($product));
        }
    }
