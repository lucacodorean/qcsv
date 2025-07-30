<?php

namespace Src\CommandLogic;
use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Src\Utils\ParameterConverter;

readonly class TruncateCommandLogic implements Command {

    public function __construct(
        private string|int $columnIdentifier,
        private int $length,
    ) { //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        $header = $initialData->getHeader();
        $keyToBeTruncated = ParameterConverter::setProperIdentifier($header, $this->columnIdentifier);

        $newDataTable = new DataTable();
        foreach ($initialData->getIterator() as $row) {
            $newRow = $row->withColumns($header);
            if(is_string($row->get($keyToBeTruncated))) {
                $newRow->set($keyToBeTruncated, mb_substr($row->get($keyToBeTruncated), 0, $this->length, 'UTF-8'));
            }
            $newDataTable->append($newRow);
        }

        return $newDataTable;
    }
}
