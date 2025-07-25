<?php

namespace Src\Commands;

use Src\Domain\DataTableInterface;

readonly class IndexCommand implements Command {
    public function __construct() {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        $index = 0;
        foreach ($initialData->getRows() as $row) {
            $row->set("id", $index++);
        }

        return $initialData;
    }
}