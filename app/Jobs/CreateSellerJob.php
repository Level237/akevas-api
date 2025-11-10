<?php

namespace App\Jobs;

use App\Models\Shop;
use App\Models\User;
use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Jobs\GenerateUniqueShopKeyJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateSellerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('CreateSellerJob started', ['email' => $this->data['email']]);

            // 👤 Création du vendeur
            $seller = new User();
            $seller->firstName = $this->data['firstName'];
            $seller->lastName = $this->data['lastName'];
            $seller->email = $this->data['email'];
            $seller->phone_number = $this->data['phone_number'];
            $seller->birthDate = $this->data['birthDate'];
            $seller->nationality = $this->data['nationality'];
            $seller->role_id = 2;
            $seller->isWholesaler = $this->data['isWholesaler'];
            $seller->password = Hash::make($this->data['password']);

            // 📄 Déplacement des fichiers vers leur dossier final
            $files = $this->data['files'] ?? [];
            $seller->identity_card_in_front = $this->moveTempFile($files['identity_card_in_front'] ?? null, 'cni/front');
            $seller->identity_card_in_back = $this->moveTempFile($files['identity_card_in_back'] ?? null, 'cni/back');
            $seller->identity_card_with_the_person = $this->moveTempFile($files['identity_card_with_the_person'] ?? null, 'cni/person');
            $seller->save();

            // 🏬 Création de la boutique
            $shop = new Shop();
            $shop->shop_name = $this->data['shop_name'];
            $shop->shop_description = $this->data['shop_description'];
            $shop->user_id = $seller->id;
            $shop->town_id = intval($this->data['town_id']);
            $shop->quarter_id = intval($this->data['quarter_id']);
            $shop->product_type = $this->data['product_type'];
            $shop->shop_gender = (string) $this->data['shop_gender'];
            $shop->shop_profile = $this->moveTempFile($files['shop_profile'] ?? null, 'shop/profile');
            $shop->save();

            // 🔗 Catégories
            if (!empty($this->data['categories'])) {
                $shop->categories()->attach($this->data['categories']);
            }

            // 🖼️ Images
            if (!empty($this->data['images'])) {
                foreach ($this->data['images'] as $imgPath) {
                    $newPath = $this->moveTempFile($imgPath, 'shop/images');
                    $img = Image::create(['image_path' => $newPath]);
                    $shop->images()->attach($img);
                }
            }

            // 🗝️ Génération de clé unique différée
            GenerateUniqueShopKeyJob::dispatch($shop->id)->delay(now()->addMinute());

            Log::info('CreateSellerJob finished successfully', ['shop_id' => $shop->id]);

        } catch (\Exception $e) {
            Log::error('CreateSellerJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function moveTempFile(?string $tempPath, string $finalDir)
    {
        if (!$tempPath || !Storage::disk('public')->exists($tempPath)) {
            return null;
        }

        $filename = basename($tempPath);
        $newPath = "$finalDir/$filename";
        Storage::disk('public')->move($tempPath, $newPath);
        return $newPath;
    }
}
