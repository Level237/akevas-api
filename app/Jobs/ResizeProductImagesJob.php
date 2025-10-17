<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ResizeProductImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Product $product;
    protected ImageManager $imageManager;

    public function __construct(Product $product)
    {
        $this->product = $product;
        // On force Imagick comme driver
        $this->imageManager = new ImageManager(new Driver());
    }

    public function handle(): void
    {
        $this->resizeProductProfile();
        $this->resizeVariationImages();
        //$this->resizeAdditionalImages();
    }

    protected function resizeProductProfile()
    {
        if (!$this->product->product_profile) return;

        try {
            $originalPath = public_path('storage/'.$this->product->product_profile);

            if (!is_readable($originalPath)) {
                Log::warning("Fichier illisible (permissions): " . $originalPath);
                return;
            }
            if (!file_exists($originalPath)) {
                Log::warning("Image profil du produit introuvable : {$originalPath}");
                return;
            }

            

            $newPath = public_path('storage/resized/products/'.basename($originalPath));
            if (!file_exists(dirname($newPath))) mkdir(dirname($newPath), 0755, true);
           
            $image = $this->imageManager->read($originalPath)
            ->scaleDown(800, 800);
        
            $canvas = $this->imageManager->create(800, 800, '#f8f8f8');
            $canvas->place($image, 'center')->save($newPath);
                
                

            //$this->product->update(['product_profile' => 'resized/products/'.basename($originalPath)]);
        } catch (\Throwable $e) {

            $imgInfo = @getimagesize($originalPath);

            if (!$imgInfo) {
                Log::warning("Image illisible : {$originalPath}");
            }
            Log::warning("Erreur redimensionnement image variation du produit {$this->product->id} : " 
            . $e->getMessage() 
            . " | Image : " . ($originalPath ?? 'inconnue'));
        }
    }

    protected function resizeVariationImages()
    {
        foreach ($this->product->variations as $variation) {
            foreach ($variation->images as $imgModel) {
                try {
                     $originalPath = public_path('storage/'.$imgModel->image_path);
                    if (!file_exists($originalPath)) {
                        Log::warning("Image variation introuvable : {$originalPath}");
                        continue;
                    }

                    $newPath = public_path('storage/resized/variations/'.basename($originalPath));
                    if (!file_exists(dirname($newPath))) mkdir(dirname($newPath), 0755, true);

                    $image = $this->imageManager->read($originalPath)
                    ->scaleDown(800, 800);
                
                    $canvas = $this->imageManager->create(800, 800, '#f8f8f8');
                    $canvas->place($image, 'center')->save($newPath);

                    //$imgModel->update(['image_path' => 'resized/variations/'.basename($originalPath)]);
                } catch (\Throwable $e) {
                    Log::warning("Erreur redimensionnement image variation du produit {$this->product->id} : " . $e->getMessage());
                }
            }
        }
    }

    protected function resizeAdditionalImages()
    {
        foreach ($this->product->images as $imgModel) {
            try {
                $originalPath = public_path('storage/'.$imgModel->image_path);
                if (!file_exists($originalPath)) {
                    Log::warning("Image additionnelle introuvable : {$originalPath}");
                    continue;
                }

                $newPath = public_path('storage/resized/images/'.basename($originalPath));
                if (!file_exists(dirname($newPath))) mkdir(dirname($newPath), 0755, true);

                $image = $this->imageManager->read($originalPath)
                ->scaleDown(800, 800);
            
                $canvas = $this->imageManager->create(800, 800, '#f8f8f8');
                $canvas->place($image, 'center')->save($newPath);

                //$imgModel->update(['image_path' => 'resized/images/'.basename($originalPath)]);
            } catch (\Throwable $e) {
                Log::warning("Erreur redimensionnement image additionnelle du produit {$this->product->id} : " . $e->getMessage());
            }
        }
    }
}
