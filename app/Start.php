<?php

namespace App;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Lumen\Http\Request;
use App\Commands\AboutCommand;
use App\Commands\MenuCommand;
use App\Commands\StartCommand;
use App\Contracts\TelegramCommand;
use App\Enums\Command;
use App\Enums\State;
use App\Handlers\FindMovieHandler;
use App\Handlers\MenuHandler;
use Illuminate\Support\Facades\DB;

class Start
{
    public function __invoke(Request $request): void
    {
        if ($this->messageIsCommand($request->dto->data)) {
            $this->runCommandHandler($request->dto);
        } else {
            $this->runStateHandler($request->dto);
        }
    }

    private function messageIsCommand(string $message): bool
    {
        return array_key_exists($message, $this->getCommands());
    }

    public function runCommandHandler(Dto $dto): void
    {
        /** @var TelegramCommand $handler */
        $handler = new ($this->getCommands()[$dto->data]);
        $handler->run($dto);
    }

    public function runStateHandler(Dto $dto): void
    {
        $user = $this->getUserByChatId($dto->chat_id);

        /** @var TelegramCommand $handler */
        $handler = new ($this->getStates()[$user->state]);
        $handler->run($dto);
    }

    private function getUserByChatId(int $chat_id): object
    {
        return DB::table('users')
            ->where('chat_id', $chat_id)
            ->first();
    }

    private function getCommands(): array
    {
        return [
            Command::Start->value => StartCommand::class,
            Command::Menu->value  => MenuCommand::class,
            Command::About->value => AboutCommand::class,
        ];
    }

    private function getStates(): array
    {
        return [
            State::Menu->value          => MenuHandler::class,
            State::FindMovie->value     => FindMovieHandler::class,
            State::MatchMovie->value    => FindMovieHandler::class,
        ];
    }
}
