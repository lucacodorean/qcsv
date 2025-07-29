<?php

namespace Src\Domain;

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
}