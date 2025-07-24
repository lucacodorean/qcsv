<?php

require __DIR__ . '/vendor/autoload.php';

use Src\CommandRunner;
use Src\Services\CsvWriterService;
use Src\Utils\ArgsParser;
use Src\Services\CsvGeneratorService;

try {
    $arguments = ArgsParser::parseArgs();

    $runner = new CommandRunner(new CsvGeneratorService(), new CsvWriterService());
    $runner->attachCommand($arguments["command"]);
    $runner->run($arguments["sourcePath"], $arguments["destinationPath"], $arguments["options"]);

} catch (InvalidArgumentException|Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}