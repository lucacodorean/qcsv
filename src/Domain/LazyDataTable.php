<?php

namespace Src\Domain;

use Generator;
class LazyDataTable implements DataTableInterface {

    public function __construct(
        private Generator $generator,
    ) {
        ///
    }

    public function getRows(): Generator {
        return $this->generator;
    }
}