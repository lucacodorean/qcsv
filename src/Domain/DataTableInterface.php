<?php

namespace Src\Domain;

use Generator;
use Ds\Vector;
use Traversable;


interface DataTableInterface {
    public function getRows(): Vector|Generator;
    public function getHeader(): array;
    public function hasHeader(): bool;
    public function getIterator(): Traversable;
}
