<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;
use packages\domain\service\common\Argon2Hash;

class UserPassword
{
    readonly string $hashedValue;

    private function __construct(string $hashedValue)
    {
        if (empty($hashedValue)) {
            throw new InvalidArgumentException('パスワードが空です。');
        }

        if (!str_starts_with($hashedValue, '$argon2')) {
            throw new InvalidArgumentException('パスワードがハッシュ化されてません。');
        }

        $this->hashedValue = $hashedValue;
    }

    public static function create(string $value): self
    {
        return new self(Argon2Hash::hashValue($value));
    }

    public static function reconstruct($hashedValue): self
    {
        return new self($hashedValue);
    }

    public function equals(UserPassword $inputedPassword): bool
    {
        return $this->hashedValue === $inputedPassword->hashedValue;
    }
}