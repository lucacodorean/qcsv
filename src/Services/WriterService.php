<?php

namespace Src\Services;

use Src\Domain\CsvFile;
use Src\Domain\Row;

interface WriterService
{
    public function writeCsv(CsvFile $csvFile): void;

    public function writeRow(Row $line): void;
}