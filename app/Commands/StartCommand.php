<?php

namespace App\Commands;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Telegram\Telegram;
use App\Contracts\TelegramCommand;
use Illuminate\Support\Facades\DB;

class StartCommand implements TelegramCommand
{
    public function run(Dto $dto): void
    {
        Telegram::send([
            'chat_id' => $dto->chat_id,
            'text'    => 'Привет, 🐰!',
        ]);

        if (DB::table('users')->where('chat_id', $dto->chat_id)->doesntExist()) {
            DB::table('users')->insert([
                'chat_id'    => $dto->chat_id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $menu = new MenuCommand();
        $menu->run($dto);
    }
}
