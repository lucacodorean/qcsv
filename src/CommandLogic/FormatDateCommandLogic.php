<?php

namespace Src\CommandLogic;

use Carbon\Carbon;
use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Carbon\Exceptions\InvalidFormatException;
use Src\Exceptions\InvalidParametersException;

readonly class FormatDateCommandLogic implements Command {

    public function __construct(
            private string $format
    ) {
        //
    }

    private function validateDateTime(string $field): bool {
        try{
            Carbon::parse($field);
            return true;
        } catch (InvalidFormatException) {
            return false;
        }
    }

    private function formatDate(string $date, string $format): string
    {
        $date = Carbon::parse($date);
        return $date->format($format);
    }

    public function execute(DataTableInterface $initialData): DataTableInterface
    {
        $headers = $initialData->getHeader();
        $hasHeader = $initialData->hasHeader();

        $formattedDataTable = new DataTable;
        try {
            foreach ($initialData->getIterator() as $row) {
                $newRow = $row->withColumns($headers);
                foreach ($newRow->getValues() as $key => $entry) {
                    if($this->validateDateTime($entry)) {
                        $newRow->set(
                            !$hasHeader ? $key : $headers[$key],
                            $this->formatDate($entry, $this->format)
                        );
                    }
                }
                $formattedDataTable->append($newRow);
            }

            return $formattedDataTable;
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
            return $initialData;
        }
    }
}
