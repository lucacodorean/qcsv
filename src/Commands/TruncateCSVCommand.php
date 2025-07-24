<?php

namespace Src\Commands;
use Src\Domain\CsvFile;
use Src\Domain\Row;
use Src\Services\StreamerService;
use Src\Services\WriterService;
use Src\Utils\HeaderWorker;

readonly class TruncateCSVCommand implements Command {

    public function __construct(
        private StreamerService $streamerService,
        private WriterService $writerService,
    ) { //
    }

    public function execute(string $filepath, string $destination = "public/output.csv", array $options = []): void
    {

        $generator = $this->streamerService->stream($filepath);
        $headers = HeaderWorker::computeHeader($generator->current());
        $csvFile = new CsvFile;

        echo "Truncating the string columns of csv file at path $filepath to length: $options[0]. \n";

        $hasHeaders = $headers != [];

        foreach ($generator as $line) {
            if($hasHeaders) {
                $hasHeaders = false;
                $csvFile->addRow(new Row($line, $headers));
                continue;
            }

            if($generator->current() == null) break;
            $rowData = [];

            foreach ($line as $entry) {
                if(is_string($entry)) {
                    $data = mb_substr($entry, 0, $options[0], 'UTF-8');
                    $rowData[] = $data;
                }
                else $rowData[] = $entry;
            }

            $csvFile->addRow(new Row($rowData, $headers));
        }

        $this->writerService->open($destination);
        $this->writerService->writeCsv($csvFile);
    }
}