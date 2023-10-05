<?php

namespace App\Commands;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Telegram\Telegram;
use App\Contracts\TelegramCommand;
use App\Enums\MenuButton;

class MenuCommand implements TelegramCommand
{
    public function run(Dto $dto): void
    {
        Telegram::send([
            'chat_id' => $dto->chat_id,
            'text'    => 'Выберите действие',
            'reply_markup' => [
                'inline_keyboard'   => [
                    [
                        [
                            'text'          => MenuButton::Find->value,
                            'callback_data' => MenuButton::Find->name,
                        ],
                    ],
                    [
                        [
                            'text'          => MenuButton::Match->value,
                            'callback_data' => MenuButton::Match->name,
                        ],
                    ],
                ],
                'one_time_keyboard' => true,
                'resize_keyboard'   => true,
            ],
        ]);
    }
}
