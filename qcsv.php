<?php

require __DIR__ . '/vendor/autoload.php';

use Src\CommandRunner;
use Src\Input\CommandInput;
use Src\Services\IO\ReadServiceImpl;
use Src\Services\IO\TableWriter;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

try {
    $commandInput = CommandInput::fromOpt();

    new CommandRunner(
        new ReadServiceImpl(),
        new TableWriter()
    )->run($commandInput);


} catch (InvalidArgumentException|Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}
