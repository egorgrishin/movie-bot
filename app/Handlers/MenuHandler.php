<?php

namespace App\Handlers;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Telegram\Telegram;
use App\Enums\MenuButton;
use App\Enums\State;
use Illuminate\Database\Query\Builder;

class MenuHandler
{
    public function run(Dto $dto): void
    {
        if ($dto->data === MenuButton::Find->name) {
            $this->findMovie($dto->chat_id);
            return;
        }
        if ($dto->data === MenuButton::Match->name) {
            $this->matchMovie($dto->chat_id);
            return;
        }
        if ($dto->data === MenuButton::Wish->name) {
            $this->wishMovies($dto->chat_id);
            return;
        }

        Telegram::send([
            'chat_id' => $dto->chat_id,
            'text'    => 'Некорректная команда(',
        ]);
    }

    private function findMovie(int $chat_id): void
    {
        $this->setState($chat_id, State::FindMovie);
        Telegram::send([
            'chat_id' => $chat_id,
            'text'    => 'Введите название фильма',
        ]);
    }

    private function matchMovie(int $chat_id): void
    {
        $this->setState($chat_id, State::MatchMovie);
        Telegram::send([
            'chat_id' => $chat_id,
            'text'    => 'В разработке',
        ]);
    }

    private function wishMovies(int $chat_id): void
    {
        $this->setState($chat_id, State::WishMovies);
        $movies = db()->table('movies')
            ->whereExists(function (Builder $query) use ($chat_id) {
                $query->select(db()->raw(1))
                    ->from('movie_user')
                    ->where('user_id', $chat_id)
                    ->whereColumn('movies.id', 'movie_user.movie_id');
            })
            ->get()
            ->pluck('name')
            ->join("\n");
        Telegram::send([
            'chat_id' => $chat_id,
            'text'    => $movies,
        ]);
    }

    private function setState(int $chat_id, State $state): void
    {
        db()->table('users')
            ->where('chat_id', $chat_id)
            ->update(['state' => $state->value]);
    }
}
