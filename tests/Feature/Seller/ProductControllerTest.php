<?php

namespace Tests\Feature\Seller;

use App\Models\User;
use Tests\Feature\TestCase;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductControllerTest extends TestCase
{
    private $user;
    private $shop;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur et un magasin pour les tests
        $this->user = User::factory()->create(['role_id' => 2]);
        $this->shop = Shop::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_create_a_product_with_images()
    {
        Storage::fake('public');

        $this->actingAs($this->user, 'api');

        $productProfile = UploadedFile::fake()->image('product.jpg');
        $images = [
            UploadedFile::fake()->image('image1.jpg'),
            UploadedFile::fake()->image('image2.jpg')
        ];

        $categories = [1, 2]; // Supposons que ces IDs de catégories existent

        $response = $this->postJson('/api/seller/products', [
            'product_name' => 'Test Product',
            'product_description' => 'Test Description',
            'shop_id' => $this->shop->id,
            'product_price' => 99.99,
            'product_quantity' => 10,
            'product_profile' => $productProfile,
            'images' => $images,
            'categories' => $categories
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Product created successfully']);

        // Vérifier que le produit existe dans la base de données
        $this->assertDatabaseHas('products', [
            'product_name' => 'Test Product',
            'shop_id' => $this->shop->id,
            'product_price' => 99.99,
            'product_quantity' => 10,
        ]);

        // Vérifier que les fichiers ont été stockés
        Storage::disk('public')->assertExists('product/profile/' . $productProfile->hashName());
        
        // Vérifier que les images ont été stockées
        foreach ($images as $image) {
            Storage::disk('public')->assertExists('shop/images/' . $image->hashName());
        }
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/seller/products', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'product_name',
                     'product_description',
                     'shop_id',
                     'product_price',
                     'product_quantity',
                     'product_profile'
                 ]);
    }
} 