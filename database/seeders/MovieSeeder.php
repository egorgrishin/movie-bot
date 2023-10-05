<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->addMovies(
            $this->getGenres(),
            $this->getCountries()
        );
    }

    private function getGenres(): Collection
    {
        return DB::table('genres')
            ->get()
            ->keyBy('name');
    }

    private function getCountries(): Collection
    {
        return DB::table('countries')
            ->get()
            ->keyBy('name');
    }

    /**
     * Добавляет фильмы в базу данных
     */
    private function addMovies(Collection $genres, Collection $countries): void
    {
        $id = 1;
        $now = date('Y-m-d H:i:s');
        $movies = [];
        $related_genres = [];
        $related_countries = [];
        $trailers = [];

        for ($i = 1; true; $i++) {
            if (!file_exists($path = __DIR__ . "/../../films/pages/$i.json")) {
                break;
            }

            $json = json_decode(file_get_contents($path), true);
            if (!array_key_exists('docs', $json)) {
                break;
            }

            foreach ($json['docs'] as $movie) {
                $movies[] = [
                    'id'                  => $id,
                    'kp_id'               => $movie['id'],
                    'type'                => $movie['type'],
                    'name'                => $movie['name'],
                    'description'         => $movie['description'],
                    'kp_rating'           => $movie['rating']['kp'],
                    'kp_votes_count'      => $movie['votes']['kp'],
                    'poster_url'          => $movie['poster']['url'] ?? null,
                    'backdrop_url'        => $movie['backdrop']['url'] ?? null,
                    'year'                => $movie['year'],
                    'age_rating'          => $movie['ageRating'],
                    'is_series'           => $movie['isSeries'],
                    'movie_length'        => $movie['movieLength'],
                    'series_length'       => $movie['seriesLength'],
                    'total_series_length' => $movie['totalSeriesLength'],
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];

                $related_genres = array_merge(
                    $related_genres,
                    $this->getMovieGenres($id, $genres, $movie['genres'])
                );
                $related_countries = array_merge(
                    $related_countries,
                    $this->getMovieCountries($id, $countries, $movie['countries'])
                );
                $trailers = array_merge(
                    $trailers,
                    $this->getMovieTrailers($id, $movie['videos']['trailers'] ?? [])
                );

                if (count($movies) == 100) {
//                    DB::table('movies')->insert($movies);
//                    DB::table('genre_movie')->insert($related_genres);
//                    DB::table('country_movie')->insert($related_countries);
//                    DB::table('trailers')->insert($trailers);
                    $movies = [];
                    $related_genres = [];
                    $related_countries = [];
                    $trailers = [];
                }

                $id++;
            }
        }
    }

    private function getMovieGenres(int $movie_id, Collection $genres, array $movie_genres): array
    {
        $data = [];
        $movie_genres = array_column($movie_genres, 'name');

        foreach ($movie_genres as $genre) {
            $data[] = [
                'genre_id' => $genres->get($genre)->id,
                'movie_id' => $movie_id,
            ];
        }

        return $data;
    }

    private function getMovieCountries(int $movie_id, Collection $countries, array $movie_countries): array
    {
        $data = [];
        $movie_countries = array_column($movie_countries, 'name');

        foreach ($movie_countries as $country) {
            $data[] = [
                'country_id' => $countries->get($country)->id,
                'movie_id'   => $movie_id,
            ];
        }

        return $data;
    }

    private function getMovieTrailers(int $movie_id, array $trailers): array
    {
        $data = [];

        foreach ($trailers as $trailer) {
            $data[] = [
                'movie_id'   => $movie_id,
                'name'       => $trailer['name'],
                'url'        => $trailer['url'],
            ];
        }

        return $data;
    }
}
