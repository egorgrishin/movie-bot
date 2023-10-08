<?php

namespace App\Exceptions;

use App\Classes\Telegram\Telegram;
use App\Contracts\TelegramException;

class ServerError extends Exception implements TelegramException
{
    public function sendMessage(): void
    {
        Telegram::send([
            'chat_id' => $this->dto->chat_id,
            'text'    => 'Ошибка :(',
        ]);
    }
}
