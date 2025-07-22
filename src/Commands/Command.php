<?php

namespace Src\Commands;
interface Command {
    public function execute(
        string $filepath,
        string $destination="public/output.csv",
        array $options = []
    ): void;
}