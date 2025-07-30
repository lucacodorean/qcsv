<?php

namespace Src\CommandLogic;

use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;

readonly class IndexCommandLogic implements Command {
    public function __construct() {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        $index = 0;
        $indexedDataTable = new DataTable;
        foreach ($initialData->getIterator() as $row) {
            $newRow = $row->withColumns($initialData->getHeader());
            $newRow->set("id", $index++);
            $indexedDataTable->append($newRow);
        }

        return $indexedDataTable;
    }
}
