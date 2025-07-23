<?php

namespace Src;

use Src\Commands\Command;
use Src\Commands\IndexCSVCommand;
use Src\Commands\PrependCSVCommand;
use Src\Commands\ReorderCSVCommand;
use Src\Services\StreamerService;
use Src\Services\WriterService;

class CommandRunner
{
    private ?Command $command = null;

    private array $commands = [
        "prepend" => PrependCSVCommand::class,
        "index"   => IndexCSVCommand::class,
        "reorder" => ReorderCSVCommand::class,
    ];

    public function __construct(
        private StreamerService $streamerService,
        private WriterService $writerService
    ) {
        //
    }

    public function attachCommand(string $command): void {
        if(!isset($this->commands[$command])) {
            echo "No such command: $command" . PHP_EOL;
            return;
        }

        $this->command = new $this->commands[$command]($this->streamerService, $this->writerService);
    }

    public function run(string $filepath, string $destination, array $options = []): void {

        if($this->command == null) {
            echo "No command attached." . PHP_EOL;
            return;
        }

        $this->command->execute($filepath, $destination, $options);
    }
}