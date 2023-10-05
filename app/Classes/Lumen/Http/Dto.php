<?php

namespace App\Classes\Lumen\Http;

class Dto
{
    public function __construct(
        public readonly int    $chat_id,
        public readonly string $data
    ) {}
}
