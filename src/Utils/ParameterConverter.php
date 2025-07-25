<?php

namespace Src\Utils;

class ParameterConverter
{
    public static function setProperIdentifier(array $firstLine, string $value): string|int {
        $properIdentifier = $value;
        $header = HeaderWorker::computeHeader($firstLine);

        if($header == [] && !is_numeric($value)) {
            echo "Can't operate with a column name. The data table has no header.";
            exit;
        }

        else if(is_numeric($value) && $header != [])  {
            $keys = array_keys($header);
            if(isset($keys[$value])) {
                $properIdentifier =  $keys[$value];
            }
        }

        return is_numeric($properIdentifier) ? (int) $properIdentifier : $properIdentifier;
    }
}