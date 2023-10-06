<?php

namespace App\Handlers;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Telegram\Telegram;
use App\Contracts\TelegramHandler;
use App\Enums\State;
use Illuminate\Support\Facades\DB;

class FindMovieHandler implements TelegramHandler
{
    public function run(Dto $dto): void
    {
        $this->setState($dto->chat_id);
        Telegram::send([
            'chat_id' => $dto->chat_id,
            'text'    => 'Введите название фильма',
        ]);
    }

    private function setState(int $chat_id): void
    {
        DB::table('users')
            ->where('chat_id', $chat_id)
            ->update(['state' => State::EnterFilmName->value]);
    }
}
