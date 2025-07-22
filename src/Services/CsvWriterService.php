<?php

namespace Src\Services;

use SplFileObject;
use Src\Domain\CsvFile;
use Src\Domain\Row;

class CsvWriterService implements WriterService
{
    private SplFileObject $file;

    public function open(string $destinationPath): void {
        $this->file = new SplFileObject($destinationPath, 'w');
    }

    public function writeCsv(CsvFile $csvFile): void {
        foreach($csvFile->getRows()->values() as $row) {
            $this->writeRow($row);
        };
    }

    public function writeRow(Row $line): void {
        $this->file->fwrite($line);
    }
}