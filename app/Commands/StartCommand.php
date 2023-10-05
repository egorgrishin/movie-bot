<?php

namespace App\Commands;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Telegram\Telegram;
use App\Contracts\TelegramCommand;

class StartCommand implements TelegramCommand
{
    public function run(Dto $dto): void
    {
        Telegram::send([
            'chat_id' => $dto->chat_id,
            'text'    => 'Это тестовое сообщение',
        ]);
    }
}
