<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_that_user_is_registered(): void
    {
        $role = 'organizer';
        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'role' => $role,
            'skills' => json_encode([
                $this->faker->word => $this->faker->sentence
            ]),
        ];

        $response = $this->json('POST', '/api/register', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                "user",
                "authorization" => [
                    "token",
                    "type"
                ]
            ]);

        $check = array_filter($payload, fn($array) => in_array($array, ['name', 'email']));

        $this->assertDatabaseHas('users', $check);
    }

    public function test_that_user_is_logged() :void
    {
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $response = $this->actingAs($user, 'api')
            ->json('POST', '/api/login', $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                "user",
                "authorization" => [
                    "token",
                    "type"
                ]
            ]);
    }
}