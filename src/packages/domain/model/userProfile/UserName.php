<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;
use packages\domain\model\userProfile\validation\UserNameValidation;

class UserName
{
    readonly string $value;

    private const MAX_USERNAME_LENGTH = 20;

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
        return new self(substr($userEmail->localPart(), 0, self::MAX_USERNAME_LENGTH));
    }

    public static function reconstruct(string $value): self
    {
        return new self($value);
    }
}