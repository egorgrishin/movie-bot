<?php

namespace App\Classes\Lumen\Http;

class DtoFactory
{
    public static function createDto(Request $request): Dto
    {
        if (self::isCommonMessage($request)) {
            $chat_id = $request->input('message.chat.id');
            $data = $request->input('message.text', '');
        } else {
            $chat_id = $request->input('callback_query.from.id');
            $data = $request->input('callback_query.data');
        }

        return new Dto($chat_id, $data);
    }

    private static function isCommonMessage(Request $request): bool
    {
        return $request->has('message.chat.id');
    }
}
