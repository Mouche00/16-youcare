<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        $this->call([
//            UserSeeder::class
//        ]);
        // \App\Models\User::factory(10)->create();

        $organizer = \App\Models\User::factory()->create([
            'name' => 'Organizer',
            'email' => 'organizer@example.net',
            'password' => bcrypt('password')

        ]);

        $volunteer = \App\Models\User::factory()->create([
            'name' => 'Volunteer',
            'email' => 'volunteer@example.net',
            'password' => bcrypt('password')
        ]);

        \App\Models\Organizer::create([
            'user_id' => $organizer->id
        ]);
        \App\Models\Volunteer::create([
            'user_id' => $volunteer->id,
            'skills' => ['leadership', 'problem-solving', 'teamwork']
        ]);

        \App\Models\Listing::factory(10)->create();

    }
}
