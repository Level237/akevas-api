<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\Feature\TestCase;

class LoginControllerTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials()
    {
        // Créer un utilisateur de test
        $user = User::factory()->create([
            'phone_number' => '0123456789',
            'role_id' => 2,
            'password' => bcrypt('password123')
        ]);

        // Simuler une requête de connexion
        $response = $this->postJson('/api/login', [
            'phone_number' => '0123456789',
            'password' => 'password123'
        ]);

        // Vérifier que la réponse est réussie
        $response->assertStatus(200);
        
        // Vérifier que la réponse contient un token
        $response->assertJsonStructure([
            'access_token',
            'token_type',
        ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        // Créer un utilisateur de test
        $user = User::factory()->create([
            'phone_number' => '0123456789',
            'role_id' => 2,
            'password' => bcrypt('password123')
        ]);

        // Simuler une requête avec des identifiants incorrects
        $response = $this->postJson('/api/login', [
            'phone_number' => '0123456789',
            'password' => 'mauvais_mot_de_passe'
        ]);

        // Vérifier que la réponse est un échec
        $response->assertStatus(401);
    }

    public function test_login_validation_requires_phone_number_and_password()
    {
        // Simuler une requête sans données
        $response = $this->postJson('/api/login', []);

        // Vérifier que la validation échoue
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'phone_number' => ['The phone number field is required.'],
                    'password' => ['The password field is required.']
                ]
            ]);
    }
} 