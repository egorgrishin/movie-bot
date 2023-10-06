<?php

namespace App\Enums;

enum State: int
{
    case Menu = 1;
    case FindMovie = 2;
    case MatchMovie = 3;

    case ShowMovie = 4;
}
