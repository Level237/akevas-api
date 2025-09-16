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
        $user=Auth::guard('api')->user();
        $seller = User::query()->with(['shops', 'shops.images', 'shops.categories'])->findOrFail($user->id);
        $shop   = $seller->shops->first();


        $notEmpty = fn($v) => !is_null($v) && (!is_string($v) || trim($v) !== ''); // garde 0/'0'
$onlyProvided = function(\Illuminate\Http\Request $r, array $keys) use ($notEmpty) {
  return array_filter($r->only($keys), $notEmpty);
};


        

        try {
            // 1) Mise à jour vendeur
            $seller->fill([
                'firstName'    => $request->input('firstName', $seller->firstName),
                'lastName'     => $request->input('lastName', $seller->lastName),
                'email'        => $request->input('email', $seller->email),
                'phone_number' => $request->input('phone_number', $seller->phone_number),
                'birthDate'    => $request->input('birthDate', $seller->birthDate),
                'nationality'  => $request->input('nationality', $seller->nationality),
            ]);

            DB::transaction(function() use ($request, $seller, $onlyProvided, $notEmpty) {
                // USER: update partiel sans null
                $userData = $onlyProvided($request, [
                  'firstName','lastName','email','phone_number','birthDate','nationality','isWholesaler'
                ]);
                if (!empty($userData)) {
                  // caster isWholesaler si présent
                  if (array_key_exists('isWholesaler', $userData)) {
                    $userData['isWholesaler'] = (string)$userData['isWholesaler'];
                  }
                  $seller->update($userData);
                }
              
                // FICHIERS USER
                if ($request->hasFile('identity_card_in_front')) {
                  $seller->identity_card_in_front = $request->file('identity_card_in_front')->store('cni/front','public');
                }
                if ($request->hasFile('identity_card_in_back')) {
                  $seller->identity_card_in_back = $request->file('identity_card_in_back')->store('cni/back','public');
                }
                if ($request->hasFile('identity_card_with_the_person')) {
                  $seller->identity_card_with_the_person = $request->file('identity_card_with_the_person')->store('cni/person','public');
                }
                $seller->save();
              
                // SHOP
                $shop = $seller->shop ?: new Shop(['user_id' => $seller->id]);
              
                $shopData = $onlyProvided($request, [
                  'shop_name','shop_description','product_type','town_id','quarter_id'
                ]);
              
                if (array_key_exists('product_type', $shopData)) {
                  $shopData['product_type'] = (string)$shopData['product_type'];
                }
               
              
                if (!empty($shopData)) {
                  $shop->fill($shopData); // fillable requis dans Shop
                }
              
                // FICHIER SHOP PROFILE
                if ($request->hasFile('shop_profile')) {
                  $shop->shop_profile = $request->file('shop_profile')->store('shop/profile','public');
                }
              
                // Localisation par nom (optionnel, uniquement si fourni et non vide)
                if ($request->filled('town')) {
                  $town = \App\Models\Town::where('town_name', $request->input('town'))->first();
                  if ($town) $shop->town_id = $town->id;
                }
                if ($request->filled('quarter')) {
                  $quarter = \App\Models\Quarter::where('quarter_name', $request->input('quarter'))->first();
                  if ($quarter) $shop->quarter_id = $quarter->id;
                }
              
                $shop->save();
              
                // Catégories: sync seulement si fourni
                if ($request->has('categories') && is_array($request->input('categories'))) {
                  $ids = collect($request->input('categories'))
                    ->map(fn($it) => is_array($it) ? ($it['id'] ?? $it['value'] ?? null) : $it)
                    ->filter()->unique()->values()->all();
                  $shop->categories()->sync($ids);
                }
              
                // Images: remplacer seulement si fichiers fournis
                if ($request->hasFile('images')) {
                  $files = $request->file('images');
                  if (is_array($files) && count($files) > 0) {
                    $shop->images()->detach();
                    $attach = [];
                    foreach ($files as $f) {
                      $img = new \App\Models\Image();
                      $img->image_path = $f->store('shop/images','public');
                      $img->save();
                      $attach[] = $img->id;
                    }
                    if ($attach) $shop->images()->attach($attach);
                  }
                }
              });

            return response()->json([
                'success' => true,
                'message' => 'Shop updated successfully',
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Shop update error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors'  => $e->getMessage(),
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
