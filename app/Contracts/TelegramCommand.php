<?php

namespace App\Contracts;

use App\Classes\Request;

interface TelegramCommand
{
    public function run(Request $request): void;
}
