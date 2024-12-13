<?php

namespace packages\domain\model\authenticationAccount;

use InvalidArgumentException;
use packages\domain\model\authenticationAccount\validation\UserEmailFormatChecker;

class UserEmail
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (UserEmailFormatChecker::invalidEmailLength($value) || UserEmailFormatChecker::invalidEmail($value)) {
            throw new InvalidArgumentException('無効なメールアドレスです。');
        }

        $this->value = $value;
    }
}