<?php

namespace Src\Commands;
use Src\Domain\CsvFile;
use Src\Domain\DataTable;
use Src\Domain\Row;
use Src\Services\StreamerService;
use Src\Services\WriterService;
use Src\Utils\HeaderWorker;
use Src\Utils\ParameterConverter;

readonly class TruncateCSVCommand implements Command {

    public function __construct(
        private string|int $columnIdentifier,
    ) { //
    }

    public function execute(DataTable $initialData): DataTable
    {
        $firstLine = $initialData->getRows()->first()->toArray();
        $keyToBeTruncated = ParameterConverter::setProperIdentifier($firstLine, $this->columnIdentifier);

//        foreach ($line as $entry) {
//            if(is_string($entry)) {
//                $data = mb_substr($entry, 0, $options[0], 'UTF-8');
//                $rowData[] = $data;
//            }
//            else $rowData[] = $entry;
//        }

        
    }
}