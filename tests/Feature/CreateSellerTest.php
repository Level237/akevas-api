<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateSellerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_can_create_a_seller(): void
    {
        Storage::fake('public');

        // Données du vendeur
        $sellerData = [
            'userName' => 'JohnDoe',
            'role_id' => 1,
            'town_id' => 1,
            'phone_number' => '1234567890',
            'email' => 'john@example.com',
            'password' => 'password123',
            'isWholesaler' => true,
            'profile' => UploadedFile::fake()->image('profile.jpg'), // Image de profil
            'cni_in_front' => UploadedFile::fake()->image('cni_front.jpg'), // CNI avant
            'cni_in_back' => UploadedFile::fake()->image('cni_back.jpg'), // CNI arrière
        ];

        // Effectuer une requête POST pour créer un vendeur
        $response = $this->postJson('/api/create/seller', [
            'userName' => 'JohnDoe',
            'town_id' => 1,
            'phone_number' => '1234567890',
            'email' => 'john@example.com',
            'password' => 'password123',
            'isWholesaler' => true,
            'profile' => UploadedFile::fake()->image('profile.jpg'), // Image de profil
            'cni_in_front' => UploadedFile::fake()->image('cni_front.jpg'), // CNI avant
            'cni_in_back' => UploadedFile::fake()->image('cni_back.jpg'), // CNI arrière
        ]);

        // Vérifier que la réponse est correcte
        $response->assertStatus(201);

        // Vérifier que le vendeur a été créé dans la base de données
        $this->assertDatabaseHas('users', [
            'userName' => 'JohnDoe',
            'email' => 'john@example.com',
            'isWholesaler' => true,
        ]);


    }
}
