<?php

namespace App\Classes\Lumen;

use App\Concerns\RoutesRequests;
use Laravel\Lumen\Application as LumenApplication;

class Application extends LumenApplication
{
    use RoutesRequests;
}
