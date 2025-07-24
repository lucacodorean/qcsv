<?php

namespace Src\Commands;

use Src\Domain\CsvFile;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;
use Src\Services\StreamerService;
use Src\Services\WriterService;
use Ds\Map;
use Src\Utils\HeaderWorker;

readonly class ReorderCSVCommand implements Command {

    public function __construct(
        private StreamerService $streamerService,
        private WriterService   $writerService,
    ) {
        //
    }

    public function execute(string $filepath, string $destination = "public/output.csv", array $options = []): void
    {
        // Given that the command handles reordering of the columns, we assume that the csv has headers.

        try {
            $generator = $this->streamerService->stream($filepath);
            if(HeaderWorker::computeHeader($generator->current()) == []) {
                echo "The csv file at path $filepath file has no valid header.";
                return;
            }

            $header = $generator->current();
            $newHeader = explode(",", $options[0]);
            $csvFile = new CsvFile();

            echo "Reordering the csv file at path $filepath with the following header: $options[0]. \n";

            foreach ($generator as $line) {
                if($generator->current() == null) break;
                $orderedRow = new Map;

                foreach ($newHeader as $key) {
                    $orderedRow->put($key,$line[HeaderWorker::retrieve_index_value($header, $key)]);
                }

                $csvFile->addRow(new Row($orderedRow->values()->toArray(), $newHeader));
            }

            $this->writerService->open($destination);
            $this->writerService->writeCsv($csvFile);
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
        }
    }
}