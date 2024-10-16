<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;

class UserName
{
    readonly string $value;

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
        return new self($userEmail->localPart());
    }

    private function invalidUserNameLength(string $name): bool
    {
        $userNameLength = mb_strlen($name, 'UTF-8');
        return $userNameLength === 0 || $userNameLength > 20;
    }

    private function isOnlyWhitespace($value): bool 
    {
        return preg_match('/^\s*$/u', $value);
    }
}