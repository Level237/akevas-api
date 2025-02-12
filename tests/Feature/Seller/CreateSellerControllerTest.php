<?php

namespace Tests\Feature\Seller;

use Tests\TestCase;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateSellerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_seller_with_shop()
    {
        Storage::fake('public');

        $sellerData = [
            'firstName' => 'Jean',
            'lastName' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'userName' => 'jeandupont',
            'phone_number' => '+33612345678',
            'birthDate' => '1990-01-01',
            'nationality' => 'French',
            'password' => 'password123',
            'isWholesaler' => true,
            
            // Images CNI
            'identity_card_in_front' => UploadedFile::fake()->image('cni_front.jpg'),
            'identity_card_in_back' => UploadedFile::fake()->image('cni_back.jpg'),
            'identity_card_with_the_person' => UploadedFile::fake()->image('cni_person.jpg'),
            
            // Données de la boutique
            'shop_name' => 'Ma Super Boutique',
            'shop_description' => 'Description de ma boutique',
            'town_id' => 1,
            'quarter_id' => 1,
            'product_type' => 'Electronics',
            'shop_profile' => UploadedFile::fake()->image('shop_profile.jpg'),
            'categories' => [1, 2, 3],
            'images' => [
                UploadedFile::fake()->image('shop1.jpg'),
                UploadedFile::fake()->image('shop2.jpg')
            ]
        ];

        $response = $this->postJson('/api/create/seller', $sellerData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'seller created successfully'
                ]);

        // Vérification en base de données
        $this->assertDatabaseHas('users', [
            'firstName' => 'Jean',
            'lastName' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'role_id' => 2,
        ]);

        $this->assertDatabaseHas('shops', [
            'shop_name' => 'Ma Super Boutique',
            'shop_description' => 'Description de ma boutique',
        ]);

        // Vérification du stockage des fichiers
        Storage::disk('public')->assertExists('cni/front/' . $sellerData['identity_card_in_front']->hashName());
        Storage::disk('public')->assertExists('cni/back/' . $sellerData['identity_card_in_back']->hashName());
        Storage::disk('public')->assertExists('cni/person/' . $sellerData['identity_card_with_the_person']->hashName());
        Storage::disk('public')->assertExists('shop/profile/' . $sellerData['shop_profile']->hashName());
    }

    public function test_seller_creation_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/create/seller', []);

        $response->assertStatus(500);
    }
} 