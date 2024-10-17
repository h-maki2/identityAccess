<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;

class UserName
{
    readonly string $value;

    private const MAX_USERNAME_LENGTH = 20;

    public function __construct(string $value) {
        if ($this->invalidUserNameLength($value)) {
            throw new InvalidArgumentException('ユーザー名が無効です。');
        }

        if ($this->isOnlyWhitespace($value)) {
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

    private function invalidUserNameLength(string $name): bool
    {
        $userNameLength = mb_strlen($name, 'UTF-8');
        return $userNameLength === 0 || $userNameLength > self::MAX_USERNAME_LENGTH;
    }

    private function isOnlyWhitespace($value): bool 
    {
        return preg_match('/^\s*$/u', $value);
    }
}