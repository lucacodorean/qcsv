<?php

namespace Src\Commands;

use Src\Domain\DataTableInterface;
use Src\Utils\ParameterConverter;

readonly class RemoveCommand implements Command {

    public function __construct(
         private string $columnIdentifier
    ) {
        //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        $firstLine = $initialData->getHeader();
        $keyToBeRemoved = ParameterConverter::setProperIdentifier($firstLine, $this->columnIdentifier);

        foreach($initialData->getRows() as $row) {
            $row->remove($keyToBeRemoved);
        }

        return $initialData;
    }
}