<?php

namespace Src\Services\IO;

use Src\Domain\DataTableInterface;

class WriteServiceImpl implements WriteService
{
    public function toStream(DataTableInterface $table, string $destinationStream): void
    {
        $handle = fopen($destinationStream, 'w');
        $hasHeaders = $table->hasHeader();

        foreach($table->getIterator() as $row) {
            if($hasHeaders) {
                fwrite($handle, implode(",", $table->getHeader()) . PHP_EOL);
                $hasHeaders = false;
                continue;
            }

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
