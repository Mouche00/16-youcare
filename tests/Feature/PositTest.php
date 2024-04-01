<?php

use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class PositTest extends TestCase
{

    public function test_that_posit_is_created()
    {
        $user = User::factory()->create();
        $organiser = $user->organizer()->create();
        $listing = Listing::factory()->create(
            [
                'organizer_id' => $organiser->id
            ]
        );

        $user = User::factory()->create();
        $volunteer = $user->volunteer()->create(
            [
                'skills' => ['teamword' , 'leader']
            ]
        );
        $payload = [
            'listing_id' => $listing->id,
            'volunteer_id' => $volunteer->id,
            'status' => 'pending'
        ];

        $response = $this->actingAs($user, 'api')
            ->json('POST', 'api/posit', $payload)
            ->assertStatus(Response::HTTP_OK);

        $check = array_filter($payload, fn ($array) => in_array($array, ['listing_id', 'volunteer_id', 'status']));
        $this->assertDatabaseHas('posits', $check);
    }
}
