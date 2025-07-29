<?php

namespace Src\Utils;

class ParameterConverter
{
    public static function setProperIdentifier(array $header, string $value): string|int {
        $properIdentifier = $value;

        if($header == [] && !is_numeric($value)) {
            echo "Can't operate with a column name. The data table has no header.";
            exit(1);
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
