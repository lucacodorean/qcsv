<?php

namespace Src\Services\IO;

use Src\Domain\DataTableInterface;

class JsonWriter implements WriteService
{

    public function toStream(DataTableInterface $table, string $destinationStream): void {

        $handle = fopen($destinationStream, 'w');
        foreach ($table->getIterator() as $row) {
            $encodedRow = json_encode($row->toArray(), JSON_PRETTY_PRINT);
            fwrite($handle, $encodedRow);
        }

        fclose($handle);
    }

    public function passMessage(string $message, string $destinationStream): void
    {
        $handle = fopen($destinationStream, 'w');
        $encodedMessage = json_encode(["message" => $message]);
        fwrite($handle, $encodedMessage);
        fclose($handle);
    }
}
