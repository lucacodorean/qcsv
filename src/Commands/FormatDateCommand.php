<?php

namespace Src\Commands;

use Carbon\Carbon;
use Src\Domain\DataTableInterface;
use Carbon\Exceptions\InvalidFormatException;
use Src\Exceptions\InvalidParametersException;
use Src\Utils\HeaderWorker;

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
        $firstLine = $initialData->getRows()->first();
        $headers = HeaderWorker::computeHeader($firstLine->toArray());
        $hasHeaders = $headers != [];

        $labels = $firstLine->getKeys();
        try {
            foreach ($initialData->getRows() as $row) {
                foreach ($row->getValues() as $key => $entry) {
                    if($this->validateDateTime($entry)) {
                        $row->set(
                            !$hasHeaders ? $key : $labels[$key],
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