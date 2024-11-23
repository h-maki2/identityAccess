<?php

namespace packages\domain\model\AuthenticationInformation;

use InvalidArgumentException;
use packages\domain\model\AuthenticationInformation\validation\UserNameValidation;

class UserName
{
    readonly string $value;

    private function __construct(string $value) {
        $validation = new UserNameValidation();

        if ($validation->invalidUserNameLength($value)) {
            throw new InvalidArgumentException('ユーザー名が無効です。');
        }

        if ($validation->onlyWhiteSpace($value)) {
            throw new InvalidArgumentException('ユーザー名が空です。');
        }

        $this->value = $value;        
    }

    /**
     * ユーザー名の初期値はメールアドレスのローカル部
     */
    public static function initialization(UserEmail $userEmail): self
    {
        return new self(substr($userEmail->localPart(), 0, UserNameValidation::maxUserNameLength()));
    }

    public static function create(string $value): self
    {
        return new self($value);
    }
}