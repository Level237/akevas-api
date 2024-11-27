<?php

namespace Tests\Feature;

use App\Models\User;

use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function it_can_login_a_user(): void
    {
        $user = User::find(1);

        $response = $this->postJson('/api/login', [
            'phone_number' => $user->phone_number,
            'password' => $user->password,
        ]);

        $response->assertStatus(200);
    }
}
