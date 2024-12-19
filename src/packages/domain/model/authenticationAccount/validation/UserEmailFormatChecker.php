<?php

namespace packages\domain\model\authenticationAccount\validation;

class UserEmailFormatChecker
{
    private const MAX_LENGTH = 255;

    public function validate(string $email): bool
    {
        if ($this->invalidEmailLength($email)) {
            return false;
        }

        if ($this->invalidEmail($email)) {
            return false;
        }

        return true;
    }

    private function invalidEmailLength(string $email): bool
    {
        if (empty($email)) {
            return true;
        }

        return mb_strlen($email, 'UTF-8') > self::MAX_LENGTH;
    }

    private function invalidEmail(string $email): bool
    {
        return !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
    }
}