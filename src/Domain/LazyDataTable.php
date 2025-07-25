<?php

namespace Src\Domain;

use Generator;
class LazyDataTable implements DataTableInterface {

    public function __construct(
        private Generator $generator,
        private string $streamPath
    ) {
        ///
    }

    public function getStreamPath(): string {
        return $this->streamPath;
    }
    public function getRows(): Generator {
        return $this->generator;
    }
}