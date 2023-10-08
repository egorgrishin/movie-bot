<?php

namespace App\Enums;

enum State: string
{
    case Menu = 'a';
    case FindMovie = 'b';
    case MatchMovie = 'c';

    case ShowMovie = 'd';
    case WishMovies = 'e';
}
