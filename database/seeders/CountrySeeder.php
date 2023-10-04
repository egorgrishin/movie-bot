<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [];
        $i = 1;

        while (true) {
            if (!file_exists($path = __DIR__ . "/../../films/pages/$i.json")) {
                break;
            }

            $json = json_decode(file_get_contents($path), true);
            if (!array_key_exists('docs', $json)) {
                break;
            }

            foreach ($json['docs'] as $movie) {
                $movie_countries = array_column($movie['countries'], 'name');
                foreach ($movie_countries as $country) {
                    if (!in_array($country, $countries)) {
                        $countries[] = $country;
                    }
                }
            }

            $i++;
        }
    }
}
