<?php

namespace Src\Commands;

use Src\Domain\CsvFile;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;
use Src\Services\StreamerService;
use Src\Services\WriterService;
use Src\Utils\HeaderWorker;

readonly class RemoveColumnCSVCommand implements Command {

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
            $headers = HeaderWorker::computeHeader($generator->current());
            $csvFile = new CsvFile();

            $columnToBeRemoved = $options[0];

            if(!is_numeric($columnToBeRemoved) && $headers != []) {
                $columnToBeRemoved = HeaderWorker::retrieve_index_value($headers, $options[0]);
            }

            if($headers == [] && !is_numeric($columnToBeRemoved)) {
                echo "Can't operate with this column name. The csv file at path $filepath has no headers. Try using a column index.";
                return;
            }

            echo "Deleting the column $columnToBeRemoved from the csv file at path $filepath... \n";
            unset($headers[$columnToBeRemoved]);

            foreach ($generator as $line) {
                if($generator->current() == null) break;

                $newLine = [];
                foreach ($line as $key => $value) {
                    if($columnToBeRemoved == $key) continue;
                    $newLine[$key] = $value;
                }

                $csvFile->addRow(new Row($newLine, $headers));
            }

            $this->writerService->open($destination);
            $this->writerService->writeCsv($csvFile);
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
        }
    }
}