<?php

namespace App;

use App\Classes\Request;
use App\Commands\AboutCommand;
use App\Commands\StartCommand;

class Start
{
    private const COMMANDS = [
        '/start' => StartCommand::class,
        '/about' => AboutCommand::class,
    ];

    public function __invoke(Request $request)
    {
        //
    }
}
