<?php

namespace Src\Commands;

use Src\Domain\CsvFile;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;
use Src\Services\StreamerService;
use Src\Services\WriterService;
use Src\Utils\HeaderWorker;

readonly class PrependCSVCommand implements Command
{
    public function __construct(
        private StreamerService $streamerService,
        private WriterService $writerService,
    ) {
        //
    }

    public function execute(string $filepath, string $destination="public/output.csv", array $options = []): void
    {
        try {
            $generator = $this->streamerService->stream($filepath);
            $newHeader = explode(",", $options[0]);

            echo "Prepending a header for csv file at path $filepath... \n";

            $csvFile = new CsvFile();
            $firstToBeSkipped = HeaderWorker::computeHeader($generator->current()) != [];

            $csvFile->addRow(new Row($newHeader, $newHeader));

            foreach ($generator as $line) {
                if($generator->current() == null) break;
                if($firstToBeSkipped) {
                    $firstToBeSkipped = false;
                    continue;
                }

                $csvFile->addRow(new Row($line, $newHeader));
            }

            $this->writerService->open($destination);
            $this->writerService->writeCsv($csvFile);
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
        }
    }
}