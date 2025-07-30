<?php

namespace Src\Command;


use Src\Domain\DataTableInterface;
use Src\Services\IO\JsonWriter;

trait Writable
{
    protected function writeFormatted(string $type, DataTableInterface $dataTable, string $destination): void
    {
        if ($type === 'json') {
            $this->jsonWriter->toStream($dataTable, $destination);
            return;
        }

        $this->tableWriter->toStream($dataTable, $destination);

    }
}
