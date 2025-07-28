<?php

namespace Src\Services\IO;

use Src\Domain\DataTableInterface;

interface WriteService {
    public function toStream(DataTableInterface $table, string $destinationStream): void;
    public function passMessage(string $message, string $destinationStream): void;
}