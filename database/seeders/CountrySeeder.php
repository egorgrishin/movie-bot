<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('countries')->insert(
            $this->getCountries()
        );
    }

    /**
     * Возвращает список стран для вставки в базу данных
     */
    private function getCountries(): array
    {
        $data = [];
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
                        $data[] = ['name' => $country];
                    }
                }
            }

            $i++;
        }

        return $data;
    }
}
