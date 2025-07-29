<?php

namespace Src\Enums;

enum DataTableStatusEnum: string
{
    case ENCRYPTED = 'ENCRYPTED';
    case DECRYPTED = 'DECRYPTED';
    case SIGNATURE_VALID = "valid";
    case SIGNATURE_INVALID = "corrupted";
}
