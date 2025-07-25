<?php

namespace Src\Services;

use Src\Domain\DataTableInterface;

interface WriteService {
    public function toStream(DataTableInterface $table, string $destinationStream): void;
    public function lazyToStream(DataTableInterface $table, string $destinationStream): void;
}