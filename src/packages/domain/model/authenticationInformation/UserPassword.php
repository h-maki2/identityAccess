<?php

namespace packages\domain\model\AuthenticationInformation;

use InvalidArgumentException;
use packages\domain\model\AuthenticationInformation\validation\UserPasswordValidation;
use packages\domain\service\common\hash\Argon2Hash;

class UserPassword
{
    readonly string $hashedValue;

    private function __construct(string $hashedValue)
    {
        if (!str_starts_with($hashedValue, '$argon2')) {
            throw new InvalidArgumentException('パスワードがハッシュ化されてません。');
        }

        $this->hashedValue = $hashedValue;
    }

    public static function create(string $value): self
    {
        $userPasswordValidation = new UserPasswordValidation($value);
        if (!$userPasswordValidation->validate()) {
            throw new InvalidArgumentException('無効なパスワードです。');
        }

        return new self(Argon2Hash::hashValue($value));
    }

    public static function reconstruct($hashedValue): self
    {
        return new self($hashedValue);
    }

    public function equals(string $inputedPassword): bool
    {
        return password_verify($inputedPassword, $this->hashedValue);
    }
}