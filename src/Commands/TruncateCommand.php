<?php

namespace Src\Commands;
use Src\Domain\DataTableInterface;
use Src\Utils\ParameterConverter;

readonly class TruncateCommand implements Command {

    public function __construct(
        private string|int $columnIdentifier,
        private int $length,
    ) { //
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        $firstLine = $initialData->getHeader();
        $keyToBeTruncated = ParameterConverter::setProperIdentifier($firstLine, $this->columnIdentifier);

        $hasHeaders = $initialData->hasHeader();
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