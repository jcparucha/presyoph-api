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
            self::INVALID => __('auth.password'),
            self::NON_EXISTENT => __('auth.failed'),
        };
    }
}
