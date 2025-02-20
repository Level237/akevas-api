<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Seeder;
use App\Services\GenerateUrlResource;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productProfiles = [
            'products/profile/bag1.jpg', 'products/profile/bag2.jpg', 'products/profile/bag3.jpg',
            'products/profile/bag4.jpg', 'products/profile/bag5.jpg', 'products/profile/bag6.jpg',
            'products/profile/bag7.jpg', 'products/profile/bag8.jpg', 'products/profile/bag9.jpg',
            'products/profile/bag10.jpg', 'products/profile/bag11.jpg', 'products/profile/bag12.jpg',
            'products/profile/bag13.jpg',
            'products/profile/dress1.webp', 'products/profile/dress2.webp', 'products/profile/dress3.webp',
            'products/profile/dress4.webp', 'products/profile/dress5.webp',
            'products/profile/shoes1.webp', 'products/profile/shoes2.webp', 'products/profile/shoes3.webp',
            'products/profile/shoes4.webp', 'products/profile/shoes5.webp'
        ];

        $productImages = [
            'product/images/images1.jpg', 'product/images/images2.jpg', 'product/images/images3.jpg',
            'product/images/images4.jpg', 'product/images/images5.jpg', 'product/images/images6.jpg',
            'product/images/images7.jpg'
        ];

        for ($i = 1; $i <= 30; $i++) {
            $userId = rand(3, 16);
            $shop = Shop::where('user_id', $userId)->first();
            
            $productName = "Produit Test " . $i;
            
            $product = Product::create([
                'product_name' => $productName,
                'product_url' => (new GenerateUrlResource())->generateUrl($productName),
                'product_description' => "Description du produit " . $i,
                'shop_id' => $shop->id,
                'product_price' => rand(1000, 100000),
                'product_quantity' => rand(1, 50),
                'product_gender' => (string)rand(0, 2),
                'product_profile' => $productProfiles[array_rand($productProfiles)],
                'status' => 1
            ]);

            // Création des images pour le produit
            $numberOfImages = rand(1, 3);
            $selectedImages = array_rand($productImages, $numberOfImages);
            if (!is_array($selectedImages)) {
                $selectedImages = [$selectedImages];
            }

            foreach ($selectedImages as $imageIndex) {
                $image = Image::create([
                    'image_path' => $productImages[$imageIndex]
                ]);
                $product->images()->attach($image->id);
            }

            // Ajout des catégories
            $numberOfCategories = rand(1, 3);
            $categories = array_rand(range(1, 167), $numberOfCategories);
            if (!is_array($categories)) {
                $categories = [$categories];
            }
            $product->categories()->attach($categories);
        }
    }
}
