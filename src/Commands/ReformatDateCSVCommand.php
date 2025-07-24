<?php

namespace Src\Commands;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Src\Domain\CsvFile;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;
use Src\Services\StreamerService;
use Src\Services\WriterService;
use Src\Utils\HeaderWorker;

readonly class ReformatDateCSVCommand implements Command {

    public function __construct(
        private StreamerService $streamerService,
        private WriterService $writerService,
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

    private function validateFormat(string $format): bool {
        try {
            $date = Carbon::parse(Carbon::now());
            return $date->format($format);
        } catch (InvalidFormatException) {
            return false;
        }
    }

    private function formatDate(string $date, string $format): string
    {
        $date = Carbon::parse($date);
        return $date->format($format);
    }

    public function execute(string $filepath, string $destination = "public/output.csv", array $options = []): void
    {
        try {

            if(!$this->validateFormat($options[0])) {
                echo "Invalid format.";
                return;
            }

            $generator = $this->streamerService->stream($filepath);
            $headers = HeaderWorker::computeHeader($generator->current());
            $csvFile = new CsvFile;

            echo "Formatting the date columns of csv file at path $filepath to the format: $options[0]. \n";

            foreach ($generator as $line) {
                if($generator->current() == null) break;

                $rowData = [];
                foreach ($line as $entry) {
                    if($this->validateDateTime($entry)) {
                        $rowData[] = $this->formatDate($entry, $options[0]);
                    }
                    else $rowData[] = $entry;
                }

                $csvFile->addRow(new Row($rowData, $headers));
            }

            $this->writerService->open($destination);
            $this->writerService->writeCsv($csvFile);
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
        }
    }
}