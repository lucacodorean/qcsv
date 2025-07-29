<?php

namespace Src\Domain;

use Traversable;

abstract class HeaderedDataTable implements DataTableInterface
{
    public function hasHeader(): bool {
        $allNumeric = false;
        foreach($this->getHeader() as $headerValue) {
            if(!is_numeric($headerValue)) {
                $allNumeric = true;
                break;
            }
        }

        return $allNumeric;
    }

    public function getIterator(): Traversable
    {
        $generator = function() {
            foreach ($this->getRows() as $row) {
                yield $row;
            }
        };

        return $generator();
    }
}
