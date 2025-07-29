<?php

namespace Src\Commands;

use Src\Domain\DataTableInterface;
use Ds\Vector;
use Src\Domain\MergedLazyDataTable;

class MergeCommandLauncher implements Command {

    public function __construct(
        public Vector $secondaryDataTable
    ) {

    }

    public function execute(DataTableInterface $initialData): DataTableInterface {
        $firstLine = $initialData->getHeader();

        $result = new MergedLazyDataTable();
        $result->addSubTable($initialData);

        foreach ($this->secondaryDataTable as $secondaryDataTable) {
            $command = new MergeCommand($firstLine, $initialData->hasHeader());
            $result->addSubTable($command->execute($secondaryDataTable));
        }

        return $result;
    }
}
