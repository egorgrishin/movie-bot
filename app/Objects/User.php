<?php

namespace App\Objects;

class User extends AbstractObject
{
    public ?int    $chat_id;
    public ?int    $state;
    public ?string $created_at;
}
