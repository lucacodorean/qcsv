<?php

namespace Src\Utils;

use InvalidArgumentException;
use Src\Enums\ConditionTypeEnum;

readonly class SelectCondition
{
    private function __construct(
        private string $column,
        private ConditionTypeEnum $conditionType,
        private string $value,
    )
    {
        ///
    }

    public static function fromOption(string $option): self {
        $option = explode(',', $option);
        if(count($option) !== 3) {
            throw new InvalidArgumentException("Selecting conditions are not completed");
        }
        try {
            return new self($option[0], ConditionTypeEnum::{$option[1]}, $option[2]);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
            exit(1);
        }
    }

    public function getColumn(): string {
        return $this->column;
    }

    public function getConditionType(): ConditionTypeEnum {
        return $this->conditionType;
    }

    public function getValue(): string {
        return $this->value;
    }
}