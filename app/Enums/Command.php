<?php

namespace App\Enums;

enum Command: string
{
    case Start = '/start';
    case Menu = '/menu';
    case About = '/about';
}
