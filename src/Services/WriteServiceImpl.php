<?php

namespace Src\Services;

use Src\Domain\DataTableInterface;

class WriteServiceImpl implements WriteService
{
    public function toStream(DataTableInterface $table, string $destinationStream): void
    {
        $handle = fopen($destinationStream, 'w');

        foreach($table->getRows() as $row) {
            fwrite($handle, $row);
        }

        fclose($handle);
    }


    public function lazyToStream(DataTableInterface $table, string $destinationStream): void {

        $handle = fopen($destinationStream, 'w');
        foreach ($table->getRows() as $row) {
            fwrite($handle, $row);
        }
        fclose($handle);
    }
}