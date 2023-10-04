<?php

namespace App\Commands;

use App\Classes\Dto;
use App\Classes\Telegram;
use App\Contracts\TelegramCommand;

class AboutCommand implements TelegramCommand
{
    public function run(Dto $dto): void
    {
        Telegram::send([
            'chat_id' => $dto->chat_id,
            'text'    => "Ваш ID: $dto->chat_id",
        ]);
    }
}
