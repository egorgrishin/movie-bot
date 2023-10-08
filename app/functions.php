<?php

use Illuminate\Database\DatabaseManager;
use Illuminate\Log\LogManager;

if (!function_exists('db')) {
    /** Возвращает DatabaseManager */
    function db(): DatabaseManager
    {
        /** @var DatabaseManager */
        return app('db');
    }
}

if (!function_exists('logger')) {
    /** Возвращает LogManager */
    function logger(): LogManager
    {
        /** @var LogManager */
        return app('log');
    }
}
