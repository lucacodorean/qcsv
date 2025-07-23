<?php

namespace Src\Utils;

class HeaderWorker {
    public static function computeHeader(array $firstLine): array
    {
        $headers = $firstLine;
        foreach($firstLine as $item) {
            if(is_numeric($item)) {
                $headers = [];
                break;
            }
        }

        return $headers;
    }

    public static function retrieve_index_value(array $headers, string $key): int {
        $index = 0;
        foreach($headers as $header) {
            if($header === $key) {
                return $index;
            }
            $index++;
        }
        return -1;
    }

}