<?php

require __DIR__ . '/vendor/autoload.php';

use Src\CommandRunner;
use Src\Input\CommandInput;
use Src\Services\IO\ReadServiceImpl;
use Src\Services\IO\WriteServiceImpl;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

try {
    $commandInput = CommandInput::fromOpt();

    new CommandRunner(
        new ReadServiceImpl(),
        new WriteServiceImpl()
    )->run($commandInput);


} catch (InvalidArgumentException|Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}