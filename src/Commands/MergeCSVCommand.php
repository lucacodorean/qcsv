<?php

namespace Src\Commands;
use Src\Exceptions\CSVMergeException;
use Src\Utils\HeaderWorker;

class MergeCSVCommand implements Command {

    private bool $headerAlreadyWritten;

    public function __construct()
    {
        $this->headerAlreadyWritten = false;
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

    public function execute(string $filepath, string $destination = "public/output.csv", array $options = []): void
    {
        $initialFileHandle = fopen($filepath, 'r');
        $secondFileHandle = fopen($options[0], 'r');
        if($initialFileHandle === false || $secondFileHandle === false) {
            echo "Can't open the one of the input files." . PHP_EOL;
            return;
        }

        if(!$this->processCSVValidation($initialFileHandle, $secondFileHandle)) {
            fclose($initialFileHandle);
            fclose($secondFileHandle);
            return;
        }

        echo "Merging the CSVs at paths $filepath and $options[0]." . PHP_EOL;

        /// This approach is good in case the script is run from the terminal.
        /// It doesn't even need to have the files open.

        /// Is this a good approach in case the command is run from web?
        /// Security concerns?
        //exec("cat $filepath > $destination", $output, $returnedCode);
        //exec("cat $options[0] >> $destination", $output, $returnedCode);

        ///Would using file_put_contents and file_get_contents be a better approach?
        /// This would store the contents of both files in memory. and in case a CSV is huge, then the used memory
        /// would be also huge.

        /// $firstFileContents = file_get_contents($filepath);
        /// $secondFileContents = file_get_contents($options[0]);
        /// $firstFileContents .= $secondFileContents;

        /// file_put_contents($destination, $firstFileContents);


        /// Another idea would be to use a temporary file that has a fixed size, and whenever the maximum size is reached
        /// we just write it into the destination and flush its content. This brings a bit of extra logic for checking
        /// if the temp file accommodates the current line.
        /// The used memory is constant. The execution time is dependent on the size of the temp file.

        rewind($initialFileHandle);
        if(!$this->headerAlreadyWritten) {
            rewind($secondFileHandle);
        }

        $tempMemory = $options[1] * 1 ?? 8 * 1024 * 1024;

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