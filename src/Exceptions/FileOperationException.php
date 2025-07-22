<?php

namespace Src\Exceptions;

use Exception;

class FileOperationException extends Exception
{
    public function __construct(
        string $message,
        private readonly array $errors = []
    ) {
        parent::__construct($message);
    }
    public function getErrors(): array {
        return $this->errors;
    }
}