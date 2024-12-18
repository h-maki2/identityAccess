<?php

namespace packages\domain\model\definitiveRegistrationConfirmation\validation;

class OneTimeTokenValueValidation
{
    private const TOKEN_VALUE_LENGTH = 26;

    public static function validate(string $oneTimeTokenValue): bool
    {
        if (strlen($oneTimeTokenValue) !== self::TOKEN_VALUE_LENGTH) {
            return false;
        }

        return true;
    }

    public static function tokenValueLength(): int
    {
        return self::TOKEN_VALUE_LENGTH;
    }
}