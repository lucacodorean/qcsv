<?php

namespace Src\Services\IO;

use Generator;
use Src\Domain\DataTable;
use Src\Domain\DataTableInterface;
use Src\Domain\Row;
use Src\Exceptions\InvalidParametersException;
use Src\Utils\HeaderWorker;

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

        while(!feof($handle)) {
            $line = fgetcsv($handle,0, ',', '"', '\\');
            if(!$line) continue;

            yield new Row($line, $headers);
        }
        fclose($handle);
    }

    public function readEncryptionKey(string $stream): string
    {
        $handle = fopen($stream, 'r');
        if($handle === false) {
            echo "Could not open stream $stream" . PHP_EOL;
            exit;
        }

        $result = stream_get_contents($handle);
        fclose($handle);
        return $result;
    }
}