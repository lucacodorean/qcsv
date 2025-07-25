<?php

namespace Src\Commands;

use Ds\Vector;
use Src\Domain\DataTable;
use Src\Exceptions\CSVMergeException;
use Src\Utils\HeaderWorker;

class MergeCSVCommand implements Command {


    public function __construct(
        private Ds\Vector $dataTables,
        private bool $headerAlreadyWritten = false
    ) {
        ///
    }

    /**
     * @throws CSVMergeException
     */
    private function validateCSV($firstHandle, $secondHandle): void {
        $firstHandleFirstLine = fgetcsv($firstHandle, 0, ',', '"', '\\');
        $secondHandleFirstLine = fgetcsv($secondHandle, 0, ',', '"', '\\');

        if(count($firstHandleFirstLine) !== count($secondHandleFirstLine)) {
            throw new CSVMergeException("Can't merge the CSVs. They have different line size");
        }

        $firstHeader = HeaderWorker::computeHeader($firstHandleFirstLine);
        $secondHeader = HeaderWorker::computeHeader($secondHandleFirstLine);

        /// Normally, if the code reaches this if statement and the csv files
        /// dont have headers, the headers array would be equal to [], and the if statement would fail
        /// and thus, the exception will not be thrown.
        if($firstHeader != $secondHeader) {
            if($firstHeader == [] || $secondHeader == []) {
                return;
            }

            throw new CSVMergeException("Can't merge the CSVs. Headers are different.");
        }

        $this->headerAlreadyWritten = true;
    }

    private function processCSVValidation($firstHandle, $secondHandle): bool {
        try {
            $this->validateCSV($firstHandle, $secondHandle);
            return true;
        } catch (CSVMergeException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    private function flushBuffer($buffer, string $destination): void {

        rewind($buffer);
        $bufferContent = stream_get_contents($buffer);
        file_put_contents($destination, $bufferContent, FILE_APPEND | LOCK_EX);

        ftruncate($buffer, 0);
        rewind($buffer);
    }

    private function writeRow($buffer, array $row, int $maximumCapacity, string $destination = "public/output.csv"): void {

        fputcsv($buffer, $row,',', '"', '\\');
        $fileStats = fstat($buffer);
        if($fileStats['size'] >= $maximumCapacity) {
            $this->flushBuffer($buffer, $destination);
        }
    }

    private function writeFile($handle, $buffer, $maximumCapacity, $destination): void {
        while(!feof($handle)) {
            $row = fgetcsv($handle, 0, ',', '"', '\\');
            if($row === false) break;

            $this->writeRow($buffer, $row, $maximumCapacity, $destination);
        }

        $this->flushBuffer($buffer, $destination);
    }

    public function execute(DataTable $initialData): DataTable
    {

        if(!$this->processCSVValidation($initialFileHandle, $secondFileHandle)) {
            fclose($initialFileHandle);
            fclose($secondFileHandle);
            return;
        }

        rewind($initialFileHandle);
        if(!$this->headerAlreadyWritten) {
            rewind($secondFileHandle);
        }

        $tempMemory = $options[1] *  1024 * 1024 ?? 8 * 1024 * 1024;

        $destinationHandle = fopen($destination, 'w');
        ftruncate($destinationHandle, 0);
        rewind($destinationHandle);
        fclose($destinationHandle);

        $buffer = fopen("php://temp/maxmemory:$tempMemory", "w+");
        $this->writeFile($initialFileHandle, $buffer, $tempMemory, $destination);

        ftruncate($buffer, 0);
        rewind($buffer);

        $this->writeFile($secondFileHandle,  $buffer, $tempMemory, $destination);

        fclose($initialFileHandle);
        fclose($secondFileHandle);

    }
}