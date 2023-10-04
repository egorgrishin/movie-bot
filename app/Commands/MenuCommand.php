<?php

namespace App\Commands;

use App\Classes\Dto;
use App\Classes\Telegram;
use App\Contracts\TelegramCommand;

class MenuCommand implements TelegramCommand
{
    public function run(Dto $dto): void
    {
        Telegram::send([
            'chat_id' => $dto->chat_id,
            'text'    => 'Меню',
        ]);
    }
}
