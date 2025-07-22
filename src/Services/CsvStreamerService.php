<?php

namespace Src\Services;

use Generator;
use SplFileObject;
use Src\Exceptions\FileNotExistsException;
use Src\Exceptions\FileOperationException;

class CsvStreamerService implements StreamerService
{

    private function validateFile(string $filePath) {
        if(!file_exists($filePath)) {
            throw new FileNotExistsException($filePath);
        }

        if(!is_readable($filePath)) {
            throw new FileOperationException("File at path $filePath is not readable.");
        }
    }

    public function stream(string $filePath): Generator
    {
        try {
            $this->validateFile($filePath);

            $file = new SplFileObject($filePath);
            $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

            foreach ($file as $row) {
                yield $row;
            }

        } catch (FileNotExistsException|FileOperationException $e) {
            echo $e->getMessage();
        }
    }
}