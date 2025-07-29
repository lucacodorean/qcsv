<?php

namespace Src\Domain;

use Ds\Map;
use http\Exception\RuntimeException;
use Src\Enums\ConditionTypeEnum;
use Src\Exceptions\InvalidParametersException;
use Src\Utils\SelectCondition;


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
        foreach ($values as $key => $value) {
            $this->fields->put(trim($headers[$key]), trim($value));
        }
    }

    public function withColumns(array $keysToSelect): self {
        $keysToSelect = array_flip($keysToSelect);

        $newFields = $this->fields->copy();
        foreach ($newFields->keys() as $key) {
            if (!isset($keysToSelect[$key])) {
                $newFields->remove($key);
            }
        }

        return new self($newFields->values()->toArray(), $newFields->keys()->toArray());
    }

    public function evaluateCondition(SelectCondition $selectCondition): bool {
        $valueToBeEvaluated = $this->get($selectCondition->getColumn());

        if (!mb_check_encoding($valueToBeEvaluated, 'UTF-8') && $selectCondition->getConditionType() == ConditionTypeEnum::REGEX) {
            throw new RuntimeException("Invalid UTF-8 input.");
        }


        return match ($selectCondition->getConditionType()) {
            ConditionTypeEnum::EQUALS => $valueToBeEvaluated == $selectCondition->getValue(),
            ConditionTypeEnum::NOT_EQUAL => $valueToBeEvaluated != $selectCondition->getValue(),
            ConditionTypeEnum::GREATER_THAN => $valueToBeEvaluated > $selectCondition->getValue(),
            ConditionTypeEnum::LOWER_THAN => $valueToBeEvaluated < $selectCondition->getValue(),
            ConditionTypeEnum::GREATER_THAN_OR_EQUAL => $valueToBeEvaluated >= $selectCondition->getValue(),
            ConditionTypeEnum::LOWER_THAN_OR_EQUAL => $valueToBeEvaluated <= $selectCondition->getValue(),
            ConditionTypeEnum::REGEX =>
                preg_match("#{$selectCondition->getValue()}#iu", $valueToBeEvaluated) === 1
        };

    }

    /**
     * @throws InvalidParametersException
     */
    public static function fromMap(Map $map): self {
        return new self($map->values()->toArray(), $map->keys()->toArray());
    }

    public function get(string|int $key): string {
        return $this->fields->get($key);
    }

    public function set(string|int $key, string $value): void {
        $this->fields->put($key, $value);
    }

    public function getValues(): array {
        return $this->fields->values()->toArray();
    }

    public function getKeys(): array {
        return $this->fields->keys()->toArray();
    }

    public function printKeys(): void {
        foreach ($this->fields as $key => $field) {
            echo  " $key => $field" . PHP_EOL;
        }
    }

    public function getFields(): Map {
        return $this->fields;
    }

    public function toArray(): array {
        return $this->fields->toArray();
    }

    public function remove(string|int $key): void {
        $this->fields->remove($key);
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