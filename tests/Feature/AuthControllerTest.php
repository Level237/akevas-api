<?php

namespace Tests\Feature;

use App\Models\User;

use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_can_login_a_user(): void
    {
        $user = User::factory()->create([
            'role_id'=>1,
            'town_id'=>1,
            'phone_number'=>'634253672',
        ]);

        $response = $this->postJson('/api/login', [
            'phone_number' => $user->phone_number,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    public function test_it_fails_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'phone_number' => "6634542432",
            'password' => "wrongpassword",
        ]);

        $response->assertStatus(400);
    }
}
