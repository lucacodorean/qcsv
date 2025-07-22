<?php

require __DIR__ . '/vendor/autoload.php';

use Src\CommandRunner;

$runner = new CommandRunner();
$runner->attachCommand("prepend");
$runner->run("temp");