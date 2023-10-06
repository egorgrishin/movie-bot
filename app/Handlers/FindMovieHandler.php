<?php

namespace App\Handlers;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Telegram\Telegram;
use App\Contracts\TelegramHandler;
use App\Enums\State;
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
        $this->setState($dto->chat_id, $search);
//        dd($buttons[5]);
    }

    private function getUser(int $chat_id): object
    {
        return DB::table('users')
            ->where('chat_id', $chat_id)
            ->first();
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

    private function setState(int $chat_id, string $message): void
    {
        DB::table('users')
            ->where('chat_id', $chat_id)
            ->update([
                'last_message' => $message,
            ]);
    }
}
