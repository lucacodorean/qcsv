<?php

namespace Src\Exceptions;
use Exception;

class FileNotExistsException extends Exception {
    public function __construct(
        string $filePath,
        private readonly array $errors = []
    ) {
        parent::__construct("File at path $filePath does not exist.");
    }
    public function getErrors(): array {
        return $this->errors;
    }
}