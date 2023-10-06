<?php

namespace App\Handlers;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Telegram\Telegram;
use App\Contracts\TelegramHandler;
use App\Enums\Number;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FindMovieHandler implements TelegramHandler
{
    public function run(Dto $dto): void
    {
        $page = 1;
        $search = $dto->data;
        $message_id = null;
        if ($this->isAction($dto->data)) {
            $data = json_decode($dto->data, true);

            if (array_key_exists('id', $data)) {
                $this->showFilm($dto, $data);
                return;
            }

            $page = (int) $data['pg'];
            $message_id = $data['mid'];
            $message = DB::table('messages')
                ->where('chat_id', $dto->chat_id)
                ->where('tg_message_id', $data['mid'])
                ->first();
            $search = $message->text;
        }


        if ($message_id === null) {
            $message_id = json_decode(Telegram::send([
                'chat_id' => $dto->chat_id,
                'text'   => 'Выбери фильм',
            ])->body(), true)['result']['message_id'];
            DB::table('messages')->insert([
                'chat_id' => $dto->chat_id,
                'tg_message_id' => $message_id,
                'text' => $search,
            ]);
        }

        $buttons = $this->getButtons(
            $message_id, $page, $search, $this->getFilms($page, $search)
        );
        Telegram::update([
            'text'    => 'Выбери фильм',
            'chat_id' => $dto->chat_id,
            'message_id' => $message_id,
            'reply_markup' => [
                'inline_keyboard'   => $buttons,
                'one_time_keyboard' => true,
                'resize_keyboard'   => true,
            ],
        ]);
//        dd($buttons);
    }

    private function isAction(string $data): bool
    {
        json_decode($data);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function getFilms(int $page, string $search): Collection
    {
        return DB::table('movies')
            ->select([
                'id',
                'name',
                DB::raw('kp_rating * kp_votes_count as rating')
            ])
            ->whereFullText('name', $search)
            ->forPage($page, 10)
            ->orderByDesc('rating')
            ->get();
    }

    private function getButtons(int $message_id, int $page, string $search, Collection $films): array
    {
        $nums = [
            Number::One->value,
            Number::Two->value,
            Number::Three->value,
            Number::Four->value,
            Number::Five->value,
            Number::Six->value,
            Number::Seven->value,
            Number::Eight->value,
            Number::Nine->value,
            Number::Ten->value,
        ];
        $buttons = [];
        for ($i = 0; $i < $films->count(); $i++) {
            $buttons[] = [[
                'text'          => $nums[$i] . ' ' . $films[$i]->name,
                'callback_data' => json_encode([
                    'pg'       => $page,
                    'id'    => $films[$i]->id,
                    'mid' => $message_id,
                    'sd'  => false,
                ]),
            ]];
        }

        $navigation = [];
        if ($page > 1) {
            $navigation[] = [
                'text'          => '◀️',
                'callback_data' => json_encode([
                    'pg'       => $page - 1,
                    'mid' => $message_id,
                ]),
            ];
        }
        $navigation[] = [
            'text'          => 'Меню',
            'callback_data' => '/menu',
        ];
        $c = $page * 10;
        $total = DB::table('movies')
            ->select('id', 'name')
            ->whereFullText('name', $search)
            ->count();
        if ($c < $total) {
            $navigation[] = [
                'text'          => '▶️',
                'callback_data' => json_encode([
                    'pg'       => $page + 1,
                    'mid' => $message_id,
                ])
            ];
        }


        $buttons[] = $navigation;
        return $buttons;
    }

    private function showFilm(Dto $dto, array $data): void
    {
        $film_id = $data['id'];
        $message_id = $data['mid'];

        if (array_key_exists('ac', $data)) {
            $action = $data['ac'];
            if (strpos($action, 'like:') !== false) {
                if (strpos($action, 'cr') !== false) {
                    $is_like = DB::table('movie_user')
                        ->where('movie_id', $film_id)
                        ->where('user_id', $dto->chat_id)
                        ->exists();
                    if (!$is_like) {
                        DB::table('movie_user')->insert([
                            'movie_id' => $film_id,
                            'user_id' => $dto->chat_id,
                        ]);
                    }
                } else {
                    DB::table('movie_user')
                        ->where('movie_id', $film_id)
                        ->where('user_id', $dto->chat_id)
                        ->delete();
                }
            }
        }

        $movie = DB::table('movies')
            ->where('id', $film_id)
            ->first();

        $genres = DB::table('genres')
            ->whereExists(function (Builder $query) use ($film_id) {
                $query->select(DB::raw(1))
                    ->from('genre_movie')
                    ->where('genre_id', $film_id)
                    ->whereColumn('genres.id', 'genre_movie.genre_id');
            })
            ->get()
            ->pluck('name')
            ->join(', ');

        $is_like = DB::table('movie_user')
            ->where('movie_id', $film_id)
            ->where('user_id', $dto->chat_id)
            ->exists();

        $genres = $genres ?: 'не указаны';

        $year = $movie->year ?: 'не указан';
        $age_rating = ($movie->age_rating . '+') ?: 'не указаны';

        $poster = $movie->poster_url ? "<a href=\"$movie->poster_url\">&#8205;</a>" : null;
        $backdrop = $movie->backdrop_url ? "<a href=\"$movie->backdrop_url\">&#8205;</a>" : '';
        $image = $poster ?: $backdrop;

        $desc = $data['sd'] ? "\n\n" . $movie->description : '';

        $message = <<<HTML
        <b>$movie->name</b>

        Жанры: $genres
        Тип: $movie->type
        Рейтинг: $movie->kp_rating
        Количество оценок: $movie->kp_votes_count
        Год: $year
        Возрастные ограничения: $age_rating
        $image
        $desc
        HTML;

        Telegram::update([
            'chat_id' => $dto->chat_id,
            'message_id' => $data['mid'],
            'text'    => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
            'reply_markup' => [
                'inline_keyboard'   => [
                    [
                        [
                            'text'          => '↩️',
                            'callback_data' => json_encode([
                                'pg'       => $data['pg'],
                                'mid' => $message_id,
                            ]),
                        ],
                        [
                            'text'          => ($data['sd'] ? 'Скрыть' : 'Показать') . ' описание',
                            'callback_data' => json_encode([
                                'pg'       => $data['pg'],
                                'id'    => $film_id,
                                'mid' => $message_id,
                                'sd'  => !$data['sd'],
                            ]),
                        ],
                    ],
                    [
                        [
                            'text'          => $is_like ? 'Удалить' : 'Добавить',
                            'callback_data' => json_encode([
                                'ac'     => 'like:' . ($is_like ? 'dl' : 'cr'),
                                'pg'       => $data['pg'],
                                'id'    => $film_id,
                                'mid' => $message_id,
                                'sd'  => $data['sd'],
                            ]),
                        ],
                    ]
                ],
                'one_time_keyboard' => true,
                'resize_keyboard'   => true,
            ],
        ]);
    }
}
