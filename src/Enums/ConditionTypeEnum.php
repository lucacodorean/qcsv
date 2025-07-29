<?php

namespace Src\Enums;

enum ConditionTypeEnum: string
{
    case EQUALS = "==";
    case NOT_EQUAL = "!=";
    case GREATER_THAN = ">";
    case LOWER_THAN = "<";
    case GREATER_THAN_OR_EQUAL = ">=";
    case LOWER_THAN_OR_EQUAL = "<=";
    case REGEX = "regex";
}
