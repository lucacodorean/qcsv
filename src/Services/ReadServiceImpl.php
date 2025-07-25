<?php

namespace Src\Services;

use Generator;
use Src\Domain\LazyDataTable;
use Src\Domain\Row;
use Src\Domain\DataTable;
use Src\Utils\HeaderWorker;
use Src\Domain\DataTableInterface;
use Src\Exceptions\InvalidParametersException;

class ReadServiceImpl implements ReadService
{
    public function read(string $stream): DataTableInterface {
        try {
            $handle = fopen($stream, 'r');
            if($handle === false) {
                echo "Could not open stream $stream" . PHP_EOL;
                exit;
            }

            $output = new DataTable();

            $firstLine = fgetcsv($handle,0, ',', '"', '\\');
            $headers = HeaderWorker::computeHeader($firstLine);

            if($headers == []) {
                echo "No headers for the stream $stream. Setting index-based." . PHP_EOL;
                $headers = range(0, count($firstLine) - 1);
            }
            $output->append(new Row($headers, $headers));

            while(!feof($handle)) {
                $line = fgetcsv($handle,0, ',', '"', '\\');
                if(!$line) continue;

                $output->append(new Row($line, $headers));
            }

            return $output;

        } catch (InvalidParametersException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function lazyRead(string $stream): Generator {
        $handle = fopen($stream, 'r');
        if($handle === false) {
            echo "Could not open stream $stream" . PHP_EOL;
            exit;
        }

        $firstLine = fgetcsv($handle,0, ',', '"', '\\');
        $headers = HeaderWorker::computeHeader($firstLine);
        if($headers == []) {
            echo "No headers for the stream $stream. Setting index-based." . PHP_EOL;
            $headers = range(0, count($firstLine) - 1);
        }


        if (flock($handle, LOCK_SH)) {
            while(!feof($handle)) {
                $line = fgetcsv($handle,0, ',', '"', '\\');
                if(!$line) continue;

                yield new Row($line, $headers);
            }
            flock($handle, LOCK_UN);
        } else {
            throw new RuntimeException("Unable to acquire shared lock");
        }
        fclose($handle);
    }
}