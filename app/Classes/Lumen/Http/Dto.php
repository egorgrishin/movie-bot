<?php

namespace App\Classes\Lumen\Http;

use App\Models\User;
use Illuminate\Http\Request;

class Dto
{
    public readonly int    $chat_id;
    public readonly string $data;
    public readonly ?User  $user;

    public static function make(Request $request): self
    {
        $dto = new self();
        if (self::isCommonMessage($request)) {
            $dto->chat_id = $request->input('message.chat.id');
            $dto->data = $request->input('message.text', '');
        } else {
            $dto->chat_id = $request->input('callback_query.from.id');
            $dto->data = $request->input('callback_query.data');
        }

        $dto->user = self::findUser($dto->chat_id);
        return $dto;
    }

    private static function isCommonMessage(Request $request): bool
    {
        return $request->has('message.chat.id');
    }

    private static function findUser(int $chat_id): ?User
    {
        /** @var User|null */
        return User::query()->find($chat_id);
    }
}
