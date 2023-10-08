<?php

namespace App\Commands;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Telegram\Button;
use App\Classes\Telegram\Telegram;
use App\Contracts\TelegramCommand;
use App\Enums\MenuButton;
use App\Enums\State;

class MenuCommand implements TelegramCommand
{
    public function run(Dto $dto): void
    {
        dd(
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
//                    [
//                        [
//                            'text'          => MenuButton::Find->value,
//                            'callback_data' => MenuButton::Find->name,
//                        ],
//                    ],
//                    [
//                        [
//                            'text'          => MenuButton::Wish->value,
//                            'callback_data' => MenuButton::Wish->name,
//                        ],
//                    ],
                    ],
                    'one_time_keyboard' => true,
                    'resize_keyboard'   => true,
                ],
//            'reply_markup' => [
//                'keyboard' => [
//                    [MenuButton::Find->value],
//                    [MenuButton::Match->value],
//                ],
//                'one_time_keyboard' => true,
//                'resize_keyboard' => true,
//            ],
            ])->body(),
            strlen(Button::get([$button])),
            "Строка - $button",
            "strlen - $strlen",
            "mb_strlen - $mb_strlen"
        );
        $this->setState($dto->chat_id);
    }

    private function setState(int $chat_id): void
    {
        db()->table('users')
            ->where('chat_id', $chat_id)
            ->update(['state' => State::Menu->value]);
    }
}
