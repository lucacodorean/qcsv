<?php

namespace Src\Commands;

use Src\Domain\CsvFile;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;
use Src\Services\StreamerService;
use Src\Services\WriterService;

readonly class PrependCSVCommand implements Command
{
    public function __construct(
        private StreamerService $streamerService,
        private WriterService $writerService,
    ) {
        //
    }

    private function computeHeader(array $firstLine): array
    {
        $headers = $firstLine;
        foreach($firstLine as $item) {
            if(is_numeric($item)) {
                $headers = [];
                break;
            }
        }

        return $headers;
    }

    public function execute(string $filePath, string $destination="public/output.csv", array $options = []): void
    {
        try {
            $headers = $this->computeHeader($this->streamerService->stream($filePath)->current());

            echo "Prepending a header for csv file at path $filePath... \n";
            $csvFile = new CsvFile();

            $firstToBeSkipped = $headers != [];
            foreach ($this->streamerService->stream($filePath) as $line) {
                if($firstToBeSkipped) {
                    $firstToBeSkipped = false;
                    continue;
                }

                $csvFile->addRow(new Row($line, $headers));
            }

            $this->writerService->open($destination);
            $this->writerService->writeRow(new Row($options, []));
            $this->writerService->writeCsv($csvFile);
        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
        }
    }
}