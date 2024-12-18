<?php

namespace packages\domain\model\definitiveRegistrationConfirmation\validation;

class OneTimePasswordValidation
{
    private const PASSWORD_LENGTH = 6;

    public static function validate(string $oneTimePassword): bool
    {
        if (strlen($oneTimePassword) !== self::PASSWORD_LENGTH) {
            return false;
        }

        return ctype_digit($oneTimePassword);
    }
}