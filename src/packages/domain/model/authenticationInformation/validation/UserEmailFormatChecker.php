<?php

namespace packages\domain\model\AuthenticationInformation\validation;

class UserEmailFormatChecker
{
    private const MIN_LENGTH = 255;

    public static function invalidEmailLength(string $email): bool
    {
        if (empty($email)) {
            return true;
        }

        return mb_strlen($email, 'UTF-8') > self::MIN_LENGTH;
    }

    public static function invalidEmail(string $email): bool
    {
        return !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
    }
}