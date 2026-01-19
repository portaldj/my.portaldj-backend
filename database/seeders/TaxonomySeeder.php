<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DJ Types
        $types = ['Club DJ', 'Mobile DJ', 'Radio DJ', 'Turntablist', 'Producer'];
        foreach ($types as $type) {
            \App\Models\DjType::firstOrCreate(['name' => $type]);
        }

        // Genres
        $genres = ['House', 'Techno', 'Trance', 'Hip Hop', 'Reggaeton', 'Pop', 'Rock', '80s', '90s'];
        foreach ($genres as $genre) {
            \App\Models\MusicGenre::firstOrCreate(['name' => $genre]);
        }

        // Countries (Demo)
        $countries = [
            ['name' => 'Chile', 'code' => 'CL', 'cities' => ['Santiago', 'Valparaíso', 'Concepción']],
            ['name' => 'United States', 'code' => 'US', 'cities' => ['New York', 'Los Angeles', 'Miami']],
        ];

        foreach ($countries as $data) {
            $country = \App\Models\Country::firstOrCreate(
                ['code' => $data['code']],
                ['name' => $data['name']]
            );

            foreach ($data['cities'] as $city) {
                \App\Models\City::firstOrCreate([
                    'country_id' => $country->id,
                    'name' => $city
                ]);
            }
        }
    }
}
