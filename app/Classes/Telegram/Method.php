<?php

namespace App\Classes\Telegram;

enum Method: string
{
    case SendMessage = 'sendMessage';
    case EditMessageText = 'editMessageText';
}
