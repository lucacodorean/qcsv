<?php

namespace Src\Domain;

use Generator;
class LazyDataTable extends HeaderedDataTable implements DataTableInterface {

    public function __construct(
        private Generator $generator,
    ) {
        ///
    }

    public function getHeader(): array {
        return $this->generator->current()->getKeys();
    }

    public function getRows(): Generator {
        return $this->generator;
    }
}