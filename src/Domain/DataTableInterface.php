<?php

namespace Src\Domain;

use Generator;
use Ds\Vector;


interface DataTableInterface {
    public function getRows(): Vector|Generator;
    public function getHeader(): array;
    public function hasHeader(): bool;
}