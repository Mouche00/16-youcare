<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'title' => fake()->title(),
            'description' => fake()->sentence(2),
            'skills' => [
                fake()->title(),
                fake()->title(),
                fake()->title(),
            ],
            'date' => fake()->date(),
            'location' => fake()->title(),
            'organizer_id' => 1
        ];
    }
}
