<?php

namespace Src\Services;

use Generator;
use Src\Domain\DataTableInterface;

interface ReadService
{
    public function read(string $stream): DataTableInterface;
    public function lazyRead(string $stream): Generator;
}