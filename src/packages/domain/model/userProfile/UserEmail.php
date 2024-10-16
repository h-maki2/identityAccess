<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;

class UserEmail
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('メールアドレスが空です。');
        }

        if ($this->invalidEmail($value)) {
            throw new InvalidArgumentException('無効なメールアドレスです。');
        }

        $this->value = $value;
    }

    public function localPart(): string
    {
        return explode('@', $this->value)[0];
    }

    private function invalidEmail(string $value): bool
    {
        return !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $value);
    }
}