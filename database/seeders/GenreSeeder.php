<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('genres')->insert(
            $this->getGenres()
        );
    }

    /**
     * Возвращает список жанров для вставки в базу данных
     */
    private function getGenres(): array
    {
        $data = [];
        $genres = [];
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
                $movie_genres = array_column($movie['genres'], 'name');
                foreach ($movie_genres as $genre) {
                    if (!in_array($genre, $genres)) {
                        $genres[] = $genre;
                        $data[] = ['name' => $genre];
                    }
                }
            }

            $i++;
        }

        return $data;
    }
}
