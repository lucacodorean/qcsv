<?php

namespace Src\Commands;
use Src\Domain\DataTableInterface;

interface Command {
    public function execute(
        DataTableInterface $initialData,
    ): DataTableInterface;
}