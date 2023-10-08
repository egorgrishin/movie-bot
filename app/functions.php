<?php

use Illuminate\Database\DatabaseManager;

if (!function_exists('db')) {
    /** Возвращает DatabaseManager */
    function db(): DatabaseManager
    {
        /** @var DatabaseManager */
        return app('db');
    }
}
