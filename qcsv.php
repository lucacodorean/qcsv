<?php

require __DIR__ . '/vendor/autoload.php';

use Src\CommandRunner;
use Src\Input\CommandInput;
use Src\Services\ReadServiceImpl;
use Src\Services\WriteServiceImpl;

try {
    $commandInput = CommandInput::fromOpt();

    if($commandInput->getCommand() == "merge") {

        $streams[] = $commandInput->getInputStream();
        $streams = array_merge($streams, explode(',' , $commandInput->getOptions()[0]));

        for($i = 0; $i < count($streams)-1; $i++) {
            $mergeCommand = CommandInput::fromCascade(
                $commandInput->getCommand(),
                $streams[$i],
                $commandInput->getDestinationStream(),
                [$streams[$i+1]]
            );

            new CommandRunner(
                new ReadServiceImpl(),
                new WriteServiceImpl()
            )->run($mergeCommand);
        }

        return;

    }

    new CommandRunner(
        new ReadServiceImpl(),
        new WriteServiceImpl()
    )->run($commandInput);


} catch (InvalidArgumentException|Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}