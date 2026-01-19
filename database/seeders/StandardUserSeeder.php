<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Spatie\Permission\Models\Role;

class StandardUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure DJ Role exists
        $djRole = Role::firstOrCreate(['name' => 'DJ']);
        $djRole->givePermissionTo(['download songs', 'access academy']);

        // Create Standard User
        $user = User::firstOrCreate(
            ['email' => 'dj@portaldj.com'],
            [
                'name' => 'Test DJ',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole($djRole);

        // Create Profile
        Profile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'username' => 'dj_test',
                'first_name' => 'Test',
                'last_name' => 'DJ',
                'phone' => '1234567890',
                // Assuming ID 1 exists for country/city/dj_type from TaxonomySeeder
                'country_id' => 1,
                'city_id' => 1,
                'dj_type_id' => 1,
                'biography' => 'I am a test DJ.',
            ]
        );

        // Attach Genres
        $genres = \App\Models\MusicGenre::take(3)->pluck('id');
        $user->genres()->sync($genres);
    }
}
