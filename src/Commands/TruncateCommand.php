<?php

namespace Src\Commands;
use Src\Domain\CsvFile;
use Src\Domain\DataTableInterface;
use Src\Domain\Row;
use Src\Services\StreamerService;
use Src\Services\WriterService;
use Src\Utils\HeaderWorker;
use Src\Utils\ParameterConverter;

readonly class TruncateCommand implements Command {

    public function __construct(
        private string|int $columnIdentifier,
        private int $length,
    ) { //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        $firstLine = $initialData->getRows()->first()->toArray();
        $keyToBeTruncated = ParameterConverter::setProperIdentifier($firstLine, $this->columnIdentifier);

        $hasHeaders = !is_numeric($keyToBeTruncated);
        foreach ($initialData->getRows() as $row) {
            if($hasHeaders) {
                $hasHeaders = false;
                continue;
            }
            if(is_string($row->get($keyToBeTruncated))) {
                $row->set($keyToBeTruncated, mb_substr($row->get($keyToBeTruncated), 0, $this->length, 'UTF-8'));
            }
        }

        return $initialData;
    }
}