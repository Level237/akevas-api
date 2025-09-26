<?php

namespace App\Http\Controllers\Seller;

use App\Models\Shop;
use App\Models\Town;
use App\Models\User;
use App\Models\Image;
use App\Models\Quarter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SellerResource;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CurrentSellerController extends Controller
{
    public function currentSeller(){

        $user=Auth::guard('api')->user();
        return SellerResource::make(User::find($user->id));
    }

    public function updateSeller(Request $request){
       
        // 1. Démarrer la transaction
        DB::beginTransaction();

        try {
           
            $user =Auth::guard('api')->user();
            $shop = $user->shop;
            
            // --- 2. Validation des Données ---
            $rules = [
                // Règles Vendeur (User)
                'firstName' => ['nullable', 'string', 'max:255'],
                'lastName' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'phone_number' => ['nullable', 'string', 'max:20'],
                'birthDate' => ['nullable', 'date'],
                'nationality' => ['nullable', 'string', 'max:100'],
                'isWholesaler' => ['nullable', 'in:0,1,2'],

                // Règles Boutique (Shop)
                'shop_name' => ['nullable', 'string', 'max:255'],
                'shop_description' => ['nullable', 'string', 'max:1000'],
                'product_type' => ['nullable', 'in:0,1,2'],
                'shop_gender' => ['nullable', 'in:1,2'], 
                'town' => ['nullable', 'string', 'max:100'],
                'quarter' => ['nullable', 'string', 'max:100'],
                // NOTE: Si le frontend envoie les IDs, utilisez 'town_id' et 'quarter_id'

                // Règles Fichiers (si un nouveau fichier est envoyé)
                'identity_card_in_front' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:5120'], 
                'identity_card_in_back' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:5120'],
                'identity_card_with_the_person' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:5120'],
                'shop_profile' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:5120'],
                'images' => ['nullable', 'array', 'max:10'], // Galerie
                'images.*' => ['file', 'mimes:jpeg,png,jpg', 'max:5120'],

                // Catégories (IDs)
                'categories' => ['nullable', 'array'],
                'categories.*' => ['integer', 'exists:categories,id'],
            ];

            $data = $request->validate($rules);

            // --- 3. Mise à jour du Vendeur (User) et des Fichiers d'Identité ---
            $userData = $request->only(['firstName', 'lastName', 'email', 'phone_number', 'birthDate', 'nationality']);
            if (isset($data['isWholesaler'])) {
                $userData['is_wholesaler'] = $data['isWholesaler'];
            }
            $user->update(array_filter($userData)); // array_filter retire les champs vides/nuls

            // Traitement des fichiers CNI/Identité
            $identityFiles = [
                'identity_card_in_front' => 'identity_card_in_front',
                'identity_card_in_back' => 'identity_card_in_back',
                'identity_card_with_the_person' => 'identity_card_with_the_person',
            ];
            foreach ($identityFiles as $requestKey => $modelAttribute) {
                if ($request->hasFile($requestKey)) {
                    // **SUPPRESSION DE L'ANCIEN FICHIER**
                    if ($user->$modelAttribute && Storage::disk('public')->exists($user->$modelAttribute)) {
                        Storage::disk('public')->delete($user->$modelAttribute);
                    }
                    // Stocker le nouveau fichier
                    $path = $request->file($requestKey)->store('seller/identity', 'public');
                    $user->$modelAttribute = $path;
                    $user->save();
                }
            }
            
            // --- 4. Mise à jour de la Boutique (Shop) et du Profil ---
            $shopData = $request->only(['shop_name', 'shop_description', 'town', 'quarter']);

            if (isset($data['product_type'])) {
                $shopData['product_type'] = $data['product_type'];
            }
            if (isset($data['shop_gender'])) {
                $shopData['gender'] = $data['shop_gender'];
            }
            
            $shop->update(array_filter($shopData));

            // Traitement de la photo de profil de la boutique
            if ($request->hasFile('shop_profile')) {
                // **SUPPRESSION DE L'ANCIEN FICHIER**
                if ($shop->shop_profile && Storage::disk('public')->exists($shop->shop_profile)) {
                    Storage::disk('public')->delete($shop->shop_profile);
                }
                // Stocker la nouvelle photo de profil
                $shop->shop_profile = $request->file('shop_profile')->store('shop/profile', 'public');
                $shop->save();
            }

            // --- 5. Synchronisation des Catégories ---
            if (isset($data['categories'])) {
                // Utilisation de sync() pour s'assurer qu'il n'y ait que les nouvelles catégories
                $shop->categories()->sync($data['categories']);
            }

            // --- 6. Traitement de la Galerie d'images ---
            // Le frontend doit gérer la suppression des anciennes images. Ici, on ajoute seulement les nouvelles.
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $imagePath = $file->store('shop/gallery', 'public');

                    // Créer un enregistrement dans la table de galerie
                    $shop->images()->create([
                        'image_path' => $imagePath // Assurez-vous que c'est le bon nom de colonne
                    ]);
                }
            }


            // 7. Valider la transaction
            DB::commit();

            // Recharger l'utilisateur avec la relation de boutique mise à jour pour la réponse
            $user->load('shop.categories', 'shop.images');

            return response()->json([
                'message' => 'Boutique et informations du vendeur mises à jour avec succès.',
                'seller_data' => $user->toArray(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur de mise à jour de boutique pour l'utilisateur {$user->id}: " . $e->getMessage());

            return response()->json([
                'message' => 'Une erreur interne est survenue lors de la mise à jour.'.$e->getMessage(),
            ], 500);
        }
    }

        
    protected function storeMaybeBase64OrFile(Request $request, string $field, ?string $existingPath, string $diskPath): ?string
    {
        // 1) fichier multipart
        if ($request->hasFile($field)) {
            $file = $request->file($field);
            return $file->store($diskPath, 'public');
        }

        // 2) base64 data URL string
        if ($request->filled($field) && is_string($request->input($field))) {
            $data = $request->input($field);
            if ($this->isDataUrl($data)) {
                return $this->storeBase64DataUrl($data, $diskPath);
            }
            // Chaîne vide => suppression
            if ($data === '') {
                return null;
            }
        }

        // sinon, on garde l’existant
        return $existingPath;
    }

    /**
     * Variante générique pour un item qui peut être un UploadedFile (dans $request->images[])
     * ou une base64 string passée directement dans le tableau (cas JSON).
     */
    protected function storeMaybeBase64OrFileGeneric($value, string $diskPath): ?string
    {
        // UploadedFile
        if (is_object($value) && method_exists($value, 'store')) {
            return $value->store($diskPath, 'public');
        }
        // base64 string
        if (is_string($value)) {
            if ($this->isDataUrl($value)) {
                return $this->storeBase64DataUrl($value, $diskPath);
            }
            // sinon on ignore (non base64)
        }
        // objet {path: 'dataurl'} venant du front
        if (is_array($value) && isset($value['path']) && is_string($value['path']) && $this->isDataUrl($value['path'])) {
            return $this->storeBase64DataUrl($value['path'], $diskPath);
        }
        return null;
    }

    protected function isDataUrl(string $value): bool
    {
        return str_starts_with($value, 'data:image/');
    }

    protected function storeBase64DataUrl(string $dataUrl, string $diskPath): string
    {
        // data:image/png;base64,XXXX
        [$meta, $content] = explode(',', $dataUrl, 2);
        $extension = 'png';
        if (preg_match('/data:image\\/(\\w+);base64/i', $meta, $m)) {
            $extension = strtolower($m[1]);
        }
        $binary = base64_decode($content);
        $filename = $diskPath.'/'.uniqid('img_').'.'.$extension;
        Storage::disk('public')->put($filename, $binary);
        return $filename;
    }

}
