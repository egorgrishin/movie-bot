<?php

namespace App\Classes\Telegram;

use Illuminate\Support\Facades\Http;

class Telegram
{
    private const URL = 'https://api.telegram.org';

    public static function send(array $data): void
    {
        $token = env('BOT_TOKEN');
        Http::post(self::URL . "/bot$token/sendMessage", $data);
    }
}
