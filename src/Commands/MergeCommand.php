<?php

namespace Src\Commands;
use Src\Domain\DataTableInterface;
use Src\Exceptions\DataTableMergeException;

class MergeCommand implements Command {

    public function __construct(
        private array $firstTableHeaders,
        private bool $firstTableHasHeaders
    )
    {

    }

    private function validateCSV(array $firstFileFirstLine, array $secondFileFirstLine, bool $hasHeaders): void {

        if(count($firstFileFirstLine) != count($secondFileFirstLine)) {
            echo count($firstFileFirstLine) . count($secondFileFirstLine) . " rows do not match\n";
            throw new DataTableMergeException("Can't merge the data tables. They have different line size.");
        }

        if($this->firstTableHasHeaders && $hasHeaders && $firstFileFirstLine != $secondFileFirstLine) {
            throw new DataTableMergeException("Can't merge the data tables. Headers are different.");
        }
    }

    public function execute(DataTableInterface $initialData): DataTableInterface {
        try {
            $currentFileFirstLine = $initialData->getHeader();
            $this->validateCSV($currentFileFirstLine, $this->firstTableHeaders, $initialData->hasHeader());
            return $initialData;
        } catch (DataTableMergeException $e) {
            echo $e->getMessage();
            exit(1);
        }
    }
}