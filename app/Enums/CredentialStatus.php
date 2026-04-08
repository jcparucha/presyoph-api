<?php

namespace App\Enums;

enum CredentialStatus
{
    case VALID;
    case INVALID;
    case NON_EXISTENT;

    public function message(): string
    {
        return match ($this) {
            self::VALID => 'Authentication successful.',
            self::INVALID => 'The password is incorrect.',
            self::NON_EXISTENT => "This user doesn't exists in our records.",
        };
    }
}
