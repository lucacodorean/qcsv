<?php

namespace Src\CommandLogic;
use Src\Domain\DataTableInterface;

interface Command {
    public function execute(
        DataTableInterface $initialData,
    ): DataTableInterface;
}
