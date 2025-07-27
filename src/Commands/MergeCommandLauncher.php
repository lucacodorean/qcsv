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
        $firstLine = $initialData->getRows()->current();

        $result = new MergedLazyDataTable();
        $result->addSubTable($initialData);

        foreach ($this->secondaryDataTable as $secondaryDataTable) {
            $result->addSubTable(
                new MergeCommand($firstLine)->execute($secondaryDataTable)
            );
        }

        return $result;
    }
}