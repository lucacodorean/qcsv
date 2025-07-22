<?php

namespace Src;

use Src\Commands\Command;
use Src\Commands\PrependCSVCommand;

class CommandRunner
{
    private ?Command $command = null;

    private array $commands = [
        "prepend" => PrependCSVCommand::class,
    ];

    public function attachCommand(string $command): void {
        if(!isset($this->commands[$command])) {
            echo "No such command: $command" . PHP_EOL;
            return;
        }

        $this->command = new $this->commands[$command]();
    }

    public function run(string $filepath, array $options = []): void {

        if($this->command == null) {
            echo "No command attached." . PHP_EOL;
            return;
        }

        $this->command->execute($filepath, $options);
    }
}