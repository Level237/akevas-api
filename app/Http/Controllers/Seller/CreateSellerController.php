<?php

namespace App\Http\Controllers\Seller;

use App\Models\Shop;
use App\Models\User;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Jobs\CreateSellerJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Jobs\GenerateUniqueShopKeyJob;
use App\Http\Requests\NewSellerRequest;
use Illuminate\Support\Facades\Storage;
use App\Services\Auth\CreateAccountSyncService;

class CreateSellerController extends Controller
{
    public function create(Request $request){

        
        try {
            Log::info('CreateSellerController started', ['email' => $request->email]);

            // 🗂️ Sauvegarde temporaire des fichiers
            $paths = [
                'identity_card_in_front' => $this->storeTempFile($request, 'identity_card_in_front'),
                'identity_card_in_back' => $this->storeTempFile($request, 'identity_card_in_back'),
                'identity_card_with_the_person' => $this->storeTempFile($request, 'identity_card_with_the_person'),
                'shop_profile' => $this->storeTempFile($request, 'shop_profile'),
            ];

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $img) {
                    $imagePaths[] = $this->storeTempFileDirect($img, 'shop/images');
                }
            }

            // 🧾 On passe des chemins, pas des fichiers
            $payload = array_merge($request->except(['identity_card_in_front', 'identity_card_in_back', 'identity_card_with_the_person', 'shop_profile', 'images']), [
                'files' => $paths,
                'images' => $imagePaths,
            ]);

            // 🧱 Dispatch du Job
            CreateSellerJob::dispatch($payload);

            return response()->json([
                'success' => true,
                'message' => 'Création du vendeur en cours...',
            ], 202);

        } catch (Exception $e) {
            Log::error('CreateSellerController failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    private function storeTempFile(Request $request, string $key)
    {
        if ($request->hasFile($key)) {
            return $request->file($key)->store("temp/$key", 'public');
        }
        return null;
    }

    private function storeTempFileDirect($file, string $path)
    {
        return $file->store("temp/$path", 'public');
    }
}
