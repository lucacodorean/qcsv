<?php

namespace Src\Commands;
interface Command {
    public function execute(string $filepath, array $options = []): void;
}