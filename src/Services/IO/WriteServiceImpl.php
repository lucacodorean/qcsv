<?php

namespace Src\Services\IO;

use Src\Domain\DataTableInterface;

class WriteServiceImpl implements WriteService
{
    public function toStream(DataTableInterface $table, string $destinationStream): void
    {
        $handle = fopen($destinationStream, 'w');

        if($table->hasHeader())
            fwrite($handle, implode(",", $table->getHeader()) . PHP_EOL);

        foreach($table->getRows() as $row) {
            fwrite($handle, $row);
        }

        fclose($handle);
    }


    public function passMessage(string $message, string $destinationStream): void {
        $handle = fopen($destinationStream, 'w');
        fwrite($handle, $message);
        fclose($handle);
    }
}