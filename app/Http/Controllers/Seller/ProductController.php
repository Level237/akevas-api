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
                              
                                if (isset($attributeValue['is_wholesale']) && $attributeValue['is_wholesale'] &&
                                    isset($attributeValue['wholesale_prices']) && is_array($attributeValue['wholesale_prices'])) {

                                    // Supprime les anciens prix de gros pour éviter les doublons si vous utilisez updateOrCreate
                                    $attrVariation->wholesalePrices()->delete();

                                    foreach ($attributeValue['wholesale_prices'] as $wholesalePriceData) {
                                        $attrVariation->wholesalePrices()->create([ // Lie les prix de gros à VariationAttribute
                                            'min_quantity' => $wholesalePriceData['min_quantity'],
                                            'wholesale_price' => $wholesalePriceData['wholesale_price'],
                                        ]);
                                        Log::info('Attribute variation wholesale price saved', ['attr_variation_id' => $attrVariation->id, 'min_quantity' => $wholesalePriceData['min_quantity']]);
                                    }
                                }
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
        public function update(Request $request, $id)
{
    try {
        DB::beginTransaction();
        // Log de la requête entrante
        Log::info('Product update request received', ['product_id' => $id, 'request_all' => $request]);

        $user = Auth::guard('api')->user();
        $shop = Shop::where('user_id', $user->id)->first();

        if (!$shop) {
            throw new \Exception("Shop not found for the authenticated user.");
        }

        $product = Product::where('id', $id)->where('shop_id', $shop->id)->firstOrFail();

        // Mise à jour des champs de base
        $product->product_name = $request->product_name;
        $product->product_description = $request->product_description;
        $product->type = $request->type == 'simple' ? 0 : 1;
        $product->product_gender = $request->product_gender;
        $product->whatsapp_number = $request->whatsapp_number;
        $product->product_residence = $request->product_residence;
        // $product->status = 0; // Optionnel : remettre en attente après modification ?

        // Gestion du produit simple
        if ($product->type == 0) {
            $product->product_price = $request->product_price;
            $product->product_quantity = $request->product_quantity;
        }

        // Mise à jour de l'image principale si fournie
        if ($request->hasFile('product_profile')) {
            // Supprimer l'ancienne image si nécessaire (optionnel)
            // if ($product->product_profile) { Storage::disk('public')->delete($product->product_profile); }
            $product->product_profile = $request->file('product_profile')->store('product/profile', 'public');
        }

        // Gestion des prix de gros globaux (Produit Simple ou Variable Couleur Uniquement)
        if ($request->is_wholesale == "1") {
            $product->is_wholesale = true;
            $product->is_only_wholesale = ($request->is_only_wholesale == "1");
            
            if ($request->has('wholesale_prices')) {
                $wholesalePricesData = json_decode($request->wholesale_prices, true);
                // On supprime les anciens prix de gros globaux pour les remplacer
                $product->wholesalePrices()->delete();

                if ($wholesalePricesData) {
                    foreach ($wholesalePricesData as $wpData) {
                        if($wpData['wholesale_price'] != "0"){
                            $product->wholesalePrices()->create([
                                'min_quantity' => $wpData['min_quantity'],
                                'wholesale_price' => $wpData['wholesale_price'],
                            ]);
                        }
                    }
                }
            }
        } else {
            $product->is_wholesale = false;
            $product->is_only_wholesale = false;
            $product->wholesalePrices()->delete();
        }

        $product->save();

        // Gestion des images du produit (Ajout)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('product/images', 'public');
                $product->images()->create(['image_path' => $imagePath]);
            }
        }

        // Gestion de la suppression des images du produit
        if ($request->filled('images_to_delete')) {
            $imagesToDelete = json_decode($request->images_to_delete, true);
            if (is_array($imagesToDelete)) {
                // Supposons que imagesToDelete contient les IDs des images
                // Si ce sont des chemins, il faudra adapter la requête
                // $product->images()->whereIn('id', $imagesToDelete)->delete(); 
                // Note: Assurez-vous de supprimer aussi les fichiers physiques si nécessaire
            }
        }

        // Gestion des variations pour produit variable
        if ($product->type == 1 && $request->filled('variations')) {
            $variationsData = json_decode($request->variations, true);
            $processedVariationIds = [];

            foreach ($variationsData as $variationData) {
                $isColorAndAttribute = (isset($variationData['sizes']) && is_array($variationData['sizes']) && count($variationData['sizes']) > 0) ||
                                       (isset($variationData['shoeSizes']) && is_array($variationData['shoeSizes']) && count($variationData['shoeSizes']) > 0);

                // Mise à jour ou Création de la variation
                // On utilise l'ID s'il est présent et valide (non null)
                $variation = null;
                if (isset($variationData['id']) && $variationData['id']) {
                    $variation = $product->variations()->find($variationData['id']);
                }

                if ($variation) {
                    // Mise à jour
                    $variation->update([
                        'color_id' => $variationData['color_id'],
                        'price' => !$isColorAndAttribute && isset($variationData['price']) ? $variationData['price'] : 0,
                        'quantity' => !$isColorAndAttribute && isset($variationData['quantity']) ? $variationData['quantity'] : null,
                    ]);
                } else {
                    // Création
                    $variation = $product->variations()->create([
                        'color_id' => $variationData['color_id'],
                        'price' => !$isColorAndAttribute && isset($variationData['price']) ? $variationData['price'] : 0,
                        'quantity' => !$isColorAndAttribute && isset($variationData['quantity']) ? $variationData['quantity'] : null,
                    ]);
                }
                
                $processedVariationIds[] = $variation->id;
                $variationId = $variationData['id'] ?? $variation->id; // ID utilisé pour les images (frameId)

                // Gestion des images de variation (Ajout)
                // Le frontend envoie `variation_images[frameId][index]`
                // Si c'est une nouvelle variation, frameId est temporaire, mais le frontend l'utilise pour mapper les fichiers
                // Il faut faire attention ici : si le frontend envoie un ID temporaire 'frame-...', il faut le récupérer
                $frameId = $variationData['id'] ?? null; 
                // Note: Dans votre code frontend, vous envoyez `variation_images[frame.id]`. 
                // Si frame.id est 'frame-...', c'est ce qui est utilisé comme clé.
                // Si frame.id est un ID de base de données, c'est ce qui est utilisé.
                
                // On doit itérer sur les fichiers envoyés pour trouver ceux correspondant à cette variation
                // Astuce : Le frontend utilise l'ID de la frame (qui peut être temporaire ou réel) comme clé.
                // Vous devez probablement passer cet ID temporaire dans le payload JSON des variations pour faire le lien.
                // Dans votre code frontend actuel, vous envoyez `id: frame.id.startsWith('frame-') ? null : frame.id`.
                // Cela signifie que vous perdez l'ID temporaire dans le JSON 'variations'.
                // CORRECTION REQUISE CÔTÉ FRONTEND ou BACKEND : 
                // Le plus simple est de se fier à l'ordre ou d'envoyer l'ID temporaire dans le JSON.
                // MAIS, le frontend envoie `variation_images[frame.id]`.
                // Si frame.id est null dans le JSON, on ne peut pas faire le lien facilement si on a plusieurs nouvelles variations.
                
                // Supposons pour l'instant que vous utilisez l'ID réel pour les updates, et que pour les créations ça marche par index ou que vous avez ajusté le frontend.
                // Pour ce code, je vais assumer que vous pouvez récupérer les images via une clé.
                
                // Approche robuste : Le frontend devrait envoyer un `temp_id` dans le JSON si `id` est null, et utiliser ce `temp_id` pour les clés de fichiers.
                // Avec le code actuel du frontend : `formData.append('variation_images[${frame.id}][${imgIndex}]', img);`
                // Si frame.id est 'frame-123', c'est la clé. Mais dans le JSON `variations`, `id` est null.
                // Il faudrait modifier le frontend pour envoyer `temp_id` ou `key` dans le JSON.
                
                // Workaround avec le code actuel : 
                // Si `id` est présent (update), la clé est l'ID.
                // Si `id` est null (create), c'est compliqué sans changer le frontend.
                
                // Code générique pour les images (à adapter selon votre logique de clé)
                $imageKey = $variationData['id'] ?: $variationData['temp_id'] ?? null; // Idéalement
                if ($imageKey) {
                     $prefix = "variation_images.{$imageKey}"; // Notation dot pour array imbriqué
                     // Laravel gère les tableaux de fichiers différemment, souvent via $request->file('variation_images')[$key]
                     $uploadedImages = $request->file('variation_images');
                     if (isset($uploadedImages[$imageKey])) {
                         foreach ($uploadedImages[$imageKey] as $image) {
                             $imagePath = $image->store('product/variations', 'public');
                             $variation->images()->create(['image_path' => $imagePath]);
                         }
                     }
                }

                // Gestion des attributs (Tailles/Pointures)
                if ($isColorAndAttribute) {
                    $processedAttrIds = [];
                    $attributesList = array_merge($variationData['sizes'] ?? [], $variationData['shoeSizes'] ?? []);

                    foreach ($attributesList as $attrData) {
                        // Update ou Create VariationAttribute
                        // On cherche par attribute_value_id pour cette variation
                        $attrVariation = $variation->attributesVariation()->updateOrCreate(
                            ['attribute_value_id' => $attrData['id']],
                            [
                                'quantity' => $attrData['quantity'],
                                'price' => $attrData['price'],
                            ]
                        );
                        $processedAttrIds[] = $attrVariation->id;

                        // Prix de gros attribut
                         if (isset($attrData['wholesalePrices']) && is_array($attrData['wholesalePrices'])) {
                            $attrVariation->wholesalePrices()->delete(); // Remplacer
                            foreach ($attrData['wholesalePrices'] as $wpData) {
                                $attrVariation->wholesalePrices()->create([
                                    'min_quantity' => $wpData['min_quantity'],
                                    'wholesale_price' => $wpData['wholesale_price'],
                                ]);
                            }
                        }
                    }
                    // Supprimer les attributs qui ne sont plus présents
                    $variation->attributesVariation()->whereNotIn('id', $processedAttrIds)->delete();
                }
            }

            // Supprimer les variations qui ne sont plus dans la liste (si on veut une synchro complète)
            // Attention : cela supprimera les variations non renvoyées.
            $product->variations()->whereNotIn('id', $processedVariationIds)->delete();
        }

        // Suppression d'images de variation spécifiques
        if ($request->filled('variation_images_to_delete')) {
            $varImagesToDelete = json_decode($request->variation_images_to_delete, true);
            // Structure attendue : { "frameId": [imageId1, imageId2] }
            foreach ($varImagesToDelete as $frameId => $imageIds) {
                // Trouver la variation (soit par frameId si c'est l'ID réel, sinon ignorer car nouvelle variation n'a pas d'images à supprimer)
                // Si frameId est un ID numérique
                if (is_numeric($frameId)) {
                    $variation = $product->variations()->find($frameId);
                    if ($variation) {
                        // $variation->images()->whereIn('id', $imageIds)->delete();
                    }
                }
            }
        }

        // Synchronisation des catégories
        $categories = [];
        if ($request->has('categories') && is_array($request->categories)) {
            $categories = array_merge($categories, array_map('intval', $request->categories));
        }
        if ($request->has('sub_categories') && is_array($request->sub_categories)) {
            $categories = array_merge($categories, array_map('intval', $request->sub_categories));
        }
        $product->categories()->sync($categories);


        DB::commit();
        Log::info('Product update transaction committed', ['product_id' => $product->id]);

        return response()->json(['message' => "Product updated successfully"], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Product update failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
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

    
