<?php

require __DIR__ . '/vendor/autoload.php';

use Src\CommandRunner;
use Src\Input\CommandInput;
use Src\Services\ReadServiceImpl;
use Src\Services\WriteServiceImpl;

try {
    $commandInput = CommandInput::fromOpt();

    new CommandRunner(
        new ReadServiceImpl(),
        new WriteServiceImpl()
    )->run($commandInput);


} catch (InvalidArgumentException|Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}