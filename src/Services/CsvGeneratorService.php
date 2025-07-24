<?php

namespace Src\Services;

use Generator;
use SplFileObject;
use Src\Exceptions\FileNotExistsException;
use Src\Exceptions\FileOperationException;

class CsvGeneratorService implements StreamerService
{

    private function validateFile(string $filePath): void {
        if(!file_exists($filePath)) {
            throw new FileNotExistsException($filePath);
        }

        if(!is_readable($filePath)) {
            throw new FileOperationException("File at path $filePath is not readable.");
        }

        if(filesize($filePath) == 0) {
            throw new FileOperationException("File at path $filePath is empty.");
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
            exit();
        }
    }
}