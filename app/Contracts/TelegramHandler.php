<?php

namespace App\Contracts;

use App\Classes\Lumen\Http\Dto;

interface TelegramHandler
{
    public function run(Dto $dto): void;
}
