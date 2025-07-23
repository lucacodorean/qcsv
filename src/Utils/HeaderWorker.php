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
}