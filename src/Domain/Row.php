<?php

namespace Src\Domain;

use Ds\Map;
use Src\Exceptions\InvalidParametersException;


/// What I'm trying to do here is to get a Row as a well-defined data structure that allows me to iterate
/// through the record.
final class Row {
    private Map $fields;

    public function __construct(
        array $values,
        array $headers = []
    ) {
        /// There are two scenarios available
        /// The first one describes the case when the CSV file has headers so that would mean that
        /// the count of values should be equal to the count of headers.
        /// The second case is there are no headers so the count of headers array is 0.

        if(count($values) !== count($headers) && !empty($headers)) {
            throw new InvalidParametersException("Given values count don't match the header count.");
        }

        $this->fields = new Map;
        if(!empty($headers)) {
            foreach ($headers as $i => $headerTag) {
                $this->fields->put($headerTag, $values[$i]);
            }
        }

        else {
            foreach ($values as $key => $value) {
                $this->fields->put($key, $value);
            }
        }
    }

    public function set(string $key, string $value): void {
        $this->fields->put($key, $value);
    }

    public function __toString(): string
    {
        $result = "";
        foreach ($this->fields as $field) {
            $result .= $field . ", ";
        }

        $result = rtrim($result, ", ");
        $result .= PHP_EOL;
        return $result;
    }
}