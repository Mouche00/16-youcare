<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateListingTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_that_listing_is_created(): void
    {
        
        $payload = [
            'title' => $this->faker->title,
            'description' => $this->faker->email,
            'date' => $this->faker->date,
            'location' => $this->faker->city,
            'competences' => [
                $this->faker->sentence,
                $this->faker->sentence,
            ]

        ];
        $user = User::factory()->create();
        $user->organizer()->create();
        $response = $this->actingAs($user , 'api')
            ->json('POST', '/api/listings', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                "listing"
            ]);

        $check = array_filter($payload, fn ($array) => in_array($array, ['title', 'description' , 'date' , 'location' , 'competences']));

        $this->assertDatabaseHas('listings', $check);
    }


}
