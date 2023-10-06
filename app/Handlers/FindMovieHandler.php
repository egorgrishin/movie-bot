<?php

namespace App\Handlers;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Telegram\Telegram;
use App\Contracts\TelegramHandler;
use App\Enums\State;
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

            if (array_key_exists('film_id', $data)) {
                $this->showFilm($dto, $data);
                return;
            }

            $page = (int) $data['page'];
            $message_id = $data['message_id'];
            $message = DB::table('messages')
                ->where('chat_id', $dto->chat_id)
                ->where('tg_message_id', $data['message_id'])
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
        Telegram::setKeyboard([
            'chat_id' => $dto->chat_id,
            'message_id' => $message_id,
            'reply_markup' => [
                'inline_keyboard'   => $buttons,
                'one_time_keyboard' => true,
                'resize_keyboard'   => true,
            ],
        ]);
        dd($buttons);
    }

    private function isAction(string $data): bool
    {
        json_decode($data);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function getFilms(int $page, string $search): Collection
    {
        return DB::table('movies')
            ->select('id', 'name')
            ->whereFullText('name', $search)
            ->forPage($page, 5)
            ->get();
    }

    private function getButtons(int $message_id, int $page, string $search, Collection $films): array
    {
        $buttons = [];
        foreach ($films as $film) {
            $buttons[] = [[
                'text'          => $film->name,
                'callback_data' => json_encode([
                    'page'       => $page,
                    'film_id'    => $film->id,
                    'message_id' => $message_id,
                    'show_desc'  => false,
                ]),
            ]];
        }

        $navigation = [];
        if ($page > 1) {
            $navigation[] = [
                'text'          => 'Назад',
                'callback_data' => json_encode([
                    'page'       => $page - 1,
                    'message_id' => $message_id,
                ]),
            ];
        }
        $c = $page * 5;
        $total = DB::table('movies')
            ->select('id', 'name')
            ->whereFullText('name', $search)
            ->count();
        if ($c < $total) {
            $navigation[] = [
                'text'          => 'Вперед',
                'callback_data' => json_encode([
                    'page'       => $page + 1,
                    'message_id' => $message_id,
                ])
            ];
        }


        $buttons[] = $navigation;
        return $buttons;
    }

    private function showFilm(Dto $dto, array $data): void
    {
        $film_id = $data['film_id'];
        $message_id = $data['message_id'];

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
        $genres = $genres ?: 'не указаны';

        $year = $movie->year ?: 'не указан';
        $age_rating = ($movie->age_rating . '+') ?: 'не указаны';

        $poster = $movie->poster_url ? "<a href=\"$movie->poster_url\">&#8205;</a>" : null;
        $backdrop = $movie->backdrop_url ? "<a href=\"$movie->backdrop_url\">&#8205;</a>" : '';
        $image = $poster ?: $backdrop;

        $desc = $data['show_desc'] ? "\n\n" . $movie->description : '';

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

        dd(json_decode(Telegram::send([
            'chat_id' => $dto->chat_id,
            'text'    => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
            'reply_markup' => [
                'inline_keyboard'   => [[
                    [
                        'text'          => 'Назад',
                        'callback_data' => json_encode([
                            'page'       => $data['page'],
                            'film_id'    => $film_id,
                            'message_id' => $message_id,
                        ]),
                    ],
                    [
                        'text'          => ($data['show_desc'] ? 'Скрыть' : 'Показать') . ' описание',
                        'callback_data' => json_encode([
                            'page'       => $data['page'],
                            'film_id'    => $film_id,
                            'message_id' => $message_id,
                            'show_desc'  => !$data['show_desc'],
                        ]),
                    ],
                ]],
                'one_time_keyboard' => true,
                'resize_keyboard'   => true,
            ],
        ])->body(), true));
    }

    private function setState(int $chat_id): void
    {
        DB::table('users')
            ->where('chat_id', $chat_id)
            ->update(['state' => State::ShowMovie->value]);
    }
}
