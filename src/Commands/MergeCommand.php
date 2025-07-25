<?php

namespace Src\Commands;
use Src\Domain\DataTableInterface;
use Src\Domain\MergedLazyDataTable;
use Src\Exceptions\DataTableMergeException;
use Src\Utils\HeaderWorker;

class MergeCommand implements Command {

    public function __construct(
        private DataTableInterface $dataTable,
    )
    {

    }

    private function validateCSV($firstFileFirstLine, $secondFileFirstLine): void {
        if(count($firstFileFirstLine) !== count($secondFileFirstLine)) {
            throw new DataTableMergeException("Can't merge the data tables. They have different line size.");
        }

        $firstHeader = HeaderWorker::computeHeader($firstFileFirstLine);
        $secondHeader = HeaderWorker::computeHeader($secondFileFirstLine);


        /// Normally, if the code reaches this if statement and the csv files
        /// dont have headers, the headers array would be equal to [], and the if statement would fail
        /// and thus, the exception will not be thrown.
        if($firstHeader != $secondHeader) {
            if($firstHeader == [] || $secondHeader == []) {
                return;
            }

            throw new DataTableMergeException("Can't merge the data tables. Headers are different.");
        }
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        try {
            $firstFileFirstLine = $initialData->getRows()->current();
            $secondFileFirstLine = $this->dataTable->getRows()->current();

            $this->validateCSV($firstFileFirstLine->getKeys(), $secondFileFirstLine->getKeys());
        } catch (DataTableMergeException $e) {
            echo $e->getMessage() . PHP_EOL;
            exit;
        }

        $mergedDataTable = new MergedLazyDataTable();

        $mergedDataTable->addSubTable($initialData);
        $mergedDataTable->addSubTable($this->dataTable);

        return $mergedDataTable;
    }
}