<?php

namespace Src\Enums;

enum DataTableStatusEnum: string
{
    case SIGNATURE_VALID = "valid";
    case SIGNATURE_INVALID = "corrupted";
}
