<?php

namespace App\Contracts;

use App\Classes\Lumen\Http\Dto;

interface TelegramCommand
{
    public function run(Dto $dto): void;
}
