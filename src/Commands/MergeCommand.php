<?php

namespace Src\Commands;
use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Src\Domain\Row;
use Src\Exceptions\DataTableMergeException;
use Src\Utils\HeaderWorker;

class MergeCommand implements Command {

    public function __construct(
        private DataTableInterface $dataTable,
        private Row $firstLineFirstFile,
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

    public function execute(DataTableInterface $initialData): DataTableInterface {
        try {
            $currentFilFirstFile = $initialData->getRows()->current();
            $this->validateCSV($currentFilFirstFile->toArray(), $this->firstLineFirstFile->toArray());
            return $initialData;
        } catch (DataTableMergeException $e) {
            echo $e->getMessage();
        }

        return new DataTable();
    }
}