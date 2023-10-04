<?php

namespace App;

use App\Classes\Dto;
use App\Classes\Request;
use App\Commands\AboutCommand;
use App\Commands\MenuCommand;
use App\Commands\StartCommand;
use App\Contracts\TelegramCommand;
use App\Enums\Command;

class Start
{
    public function __invoke(Request $request): void
    {
        if ($this->messageIsCommand($request->dto->data)) {
            $this->runCommandHandler($request->dto);
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

    private function getCommands(): array
    {
        return [
            Command::Start->value => StartCommand::class,
            Command::Menu->value  => MenuCommand::class,
            Command::About->value => AboutCommand::class,
        ];
    }
}
