<?php

namespace packages\domain\model\authenticationInformaion;

use InvalidArgumentException;
use packages\domain\model\authenticationInformaion\validation\UserEmailValidation;

class UserEmail
{
    readonly string $value;

    public function __construct(string $value, UserEmailValidation $userEmailValidation)
    {
        if (!$userEmailValidation->validate()) {
            throw new InvalidArgumentException('無効なメールアドレスです。');
        }

        $this->value = $value;
    }
}