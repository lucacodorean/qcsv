<?php

namespace Src\Commands;

use Src\Domain\CsvFile;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;
use Src\Services\StreamerService;
use Src\Services\WriterService;
use Src\Utils\HeaderWorker;

readonly class IndexCSVCommand implements Command {
    public function __construct(
        private StreamerService $streamerService,
        private WriterService $writerService,
    ) {
        //
    }

    public function execute(string $filepath, string $destination = "public/output.csv", array $options = []): void
    {
        try {
            $generator = $this->streamerService->stream($filepath);
            echo "Indexing the csv file at path $filepath... \n";

            $headers = HeaderWorker::computeHeader($generator->current());
            $hasHeaders = $headers != [];

            $csvFile = new CsvFile();
            $index = 1;

            if($hasHeaders) {
                $headers[] = "id";
                $csvFile->addRow(new Row($headers, $headers));
            }

            foreach ($generator as $line) {
                if($generator->current() == null) break;
                if($hasHeaders) {
                    $hasHeaders = false;
                    continue;
                }

                $line[] = $index++;
                $currentRow = new Row((array)$line, $headers);
                $csvFile->addRow($currentRow);
            }

            $this->writerService->open($destination);
            $this->writerService->writeCsv($csvFile);
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
        }
    }
}