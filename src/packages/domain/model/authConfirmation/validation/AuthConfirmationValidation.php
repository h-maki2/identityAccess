<?php

namespace packages\domain\model\authConfirmation\validation;

use DateTimeImmutable;
use packages\domain\model\authConfirmation\AuthConfirmation;

class AuthConfirmationValidation
{
    /**
     * 認証確認が有効かどうかを検証する
     */
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