<?php

namespace Src\Commands;

use Src\Domain\CsvFile;
use Src\Domain\DataTable;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;
use Src\Input\CommandInput;
use Src\Services\StreamerService;
use Src\Services\WriterService;
use Src\Utils\HeaderWorker;

readonly class PrependCSVCommand implements Command
{
    public function __construct(
        private array $newHeader,
    ) {
        //
    }

    public function execute(DataTable $initialData): DataTable
    {
        try {
            $initialData->prepend(new Row($this->newHeader, $this->newHeader));
            return $initialData;
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
            return new DataTable();
        }
    }
}