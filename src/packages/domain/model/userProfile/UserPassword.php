<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;
use packages\domain\model\userProfile\validation\UserPasswordValidation;
use packages\domain\service\common\Argon2Hash;

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
        $validtion = new UserPasswordValidation();
        if ($validtion->invalidPasswordLength($value)) {
            throw new InvalidArgumentException('適切なパスワードではありません。');
        }
        if ($validtion->invalidPasswordStrength($value)) {
            throw new InvalidArgumentException('適切なパスワードではありません。');
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