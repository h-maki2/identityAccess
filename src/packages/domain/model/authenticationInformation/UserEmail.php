<?php

namespace packages\domain\model\authenticationInformation;

use InvalidArgumentException;
use packages\domain\model\authenticationInformation\validation\UserEmailFormatChecker;

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