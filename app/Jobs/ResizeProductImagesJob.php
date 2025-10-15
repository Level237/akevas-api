<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ResizeProductImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     protected $product;

     public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->resizeProductProfile();
        $this->resizeVariationImages();
        $this->resizeAdditionalImages();
    }

    protected function resizeProductProfile()
    {
        if (!$this->product->product_profile) return;

        $originalPath = storage_path('app/public/' . $this->product->product_profile);
        $newPath = storage_path('app/public/resized/' . basename($originalPath));

        // Création dossier si pas existant
        if (!file_exists(dirname($newPath))) mkdir(dirname($newPath), 0755, true);

        $img = Image::make($originalPath)
            ->resize(800, 600, fn($c) => $c->aspectRatio()->upsize())
            ->resizeCanvas(800, 600, 'center', false, '#f8f8f8')
            ->save($newPath);

        // Mettre à jour le champ product_profile
        $this->product->update(['product_profile' => 'resized/' . basename($originalPath)]);
    }

    protected function resizeVariationImages()
    {
        foreach ($this->product->variations as $variation) {
            foreach ($variation->images as $imgModel) {
                $originalPath = storage_path('app/public/' . $imgModel->image_path);
                $newPath = storage_path('app/public/resized/' . basename($originalPath));

                $img = Image::make($originalPath)
                    ->resize(800, 600, fn($c) => $c->aspectRatio()->upsize())
                    ->resizeCanvas(800, 600, 'center', false, '#f8f8f8')
                    ->save($newPath);

                $imgModel->update(['image_path' => 'resized/' . basename($originalPath)]);
            }
        }
    }

    protected function resizeAdditionalImages()
    {
        foreach ($this->product->images as $imgModel) {
            $originalPath = storage_path('app/public/' . $imgModel->image_path);
            $newPath = storage_path('app/public/resized/' . basename($originalPath));

            $img = Image::make($originalPath)
                ->resize(800, 600, fn($c) => $c->aspectRatio()->upsize())
                ->resizeCanvas(800, 600, 'center', false, '#f8f8f8')
                ->save($newPath);

            $imgModel->update(['image_path' => 'resized/' . basename($originalPath)]);
        }
    }
}
