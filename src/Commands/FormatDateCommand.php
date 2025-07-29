<?php

namespace Src\Commands;

use Carbon\Carbon;
use Src\Domain\DataTableInterface;
use Carbon\Exceptions\InvalidFormatException;
use Src\Exceptions\InvalidParametersException;

readonly class FormatDateCommand implements Command {

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

        try {
            foreach ($initialData->getRows() as $row) {
                foreach ($row->getValues() as $key => $entry) {
                    if($this->validateDateTime($entry)) {
                        $row->set(
                            !$hasHeader ? $key : $headers[$key],
                            $this->formatDate($entry, $this->format)
                        );
                    }
                }
            }

            return $initialData;
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
            return $initialData;
        }
    }
}