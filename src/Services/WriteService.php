<?php

namespace Src\Services;

use Src\Domain\DataTableInterface;
use Src\Domain\EncryptedDataTable;

interface WriteService {
    public function toStream(DataTableInterface $table, string $destinationStream): void;
    public function lazyToStream(DataTableInterface $table, string $destinationStream): void;
    public function passMessage(string $message, string $destinationStream): void;
}