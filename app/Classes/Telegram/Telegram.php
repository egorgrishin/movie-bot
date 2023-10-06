<?php

namespace App\Classes\Telegram;

use Illuminate\Support\Facades\Http;

class Telegram
{
    private const URL = 'https://api.telegram.org';

    public static function send(array $data)
    {
        $token = env('BOT_TOKEN');
        return Http::post(self::URL . "/bot$token/sendMessage", $data);
    }

    public static function update(array $data)
    {
        $token = env('BOT_TOKEN');
        return Http::post(self::URL . "/bot$token/editMessageText", $data);
    }

    public static function setKeyboard(array $data)
    {
        $token = env('BOT_TOKEN');
        return Http::post(self::URL . "/bot$token/editMessageReplyMarkup", $data);
    }
}
