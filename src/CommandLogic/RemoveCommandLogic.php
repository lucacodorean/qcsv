<?php

namespace Src\CommandLogic;

use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Src\Utils\ParameterConverter;

readonly class RemoveCommandLogic implements Command {

    public function __construct(
         private string $columnIdentifier
    ) {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        $firstLine = $initialData->getHeader();
        $keyToBeRemoved = ParameterConverter::setProperIdentifier($firstLine, $this->columnIdentifier);

        $columnsKept = array_filter($initialData->getHeader(), fn($item) => !str_ends_with($item, $keyToBeRemoved));

        $newDataTable = new DataTable();
        foreach($initialData->getIterator() as $row) {
            $newRow = $row->withColumns($columnsKept);
            $newDataTable->append($newRow);
        }

        return $newDataTable;
    }
}
