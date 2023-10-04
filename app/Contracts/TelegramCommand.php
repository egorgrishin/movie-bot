<?php

namespace App\Contracts;

use App\Classes\Dto;

interface TelegramCommand
{
    public function run(Dto $dto): void;
}
