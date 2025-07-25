<?php

namespace Src\Services;

use Src\Domain\DataTable;

interface ReadService
{
    public function read(string $stream): DataTable;
}