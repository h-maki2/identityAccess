<?php

namespace packages\domain\model\authenticationAccount;

use InvalidArgumentException;
use packages\domain\model\authenticationAccount\validation\UserEmailFormatChecker;
use packages\domain\model\authenticationAccount\validation\UserEmailFormatValidation;

class UserEmail
{
    readonly string $value;

    public function __construct(string $value)
    {
        $emailChecker = new UserEmailFormatChecker();
        if (!$emailChecker->validate($value)) {
            throw new InvalidArgumentException('無効なメールアドレスです。');
        }

        $this->value = $value;
    }
}