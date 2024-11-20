<?php

namespace packages\domain\model\authConfirmation\validation;

use DateTimeImmutable;
use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\OneTimePassword;

class AuthConfirmationValidation
{
    
    public static function validateExpirationDate(?AuthConfirmation $authConfirmation, DateTimeImmutable $currentDateTime): bool
    {
        if ($authConfirmation === null) {
            return false;
        }

        if ($authConfirmation->isExpired($currentDateTime)) {
            return false;
        }

        return true;
    }
}