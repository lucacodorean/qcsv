<?php

namespace Src\Exceptions;
use Exception;

class CSVMergeException extends Exception {
    public function __construct(
        string $message,
        private readonly array $errors = []
    ) {
        parent::__construct($message . PHP_EOL);
    }
    public function getErrors(): array {
        return $this->errors;
    }
}