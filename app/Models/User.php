<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $chat_id
 */
class User extends Model
{
    protected $primaryKey = 'chat_id';

    protected $fillable = [
        'chat_id',
    ];

    protected $visible = [
        'state',
    ];

    public const UPDATED_AT = null;
}
