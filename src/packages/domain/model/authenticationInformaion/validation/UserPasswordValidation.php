<?php

namespace packages\domain\model\authenticationInformaion\validation;

class UserPasswordValidation
{
    private const MIN_LENGTH = 8;

    /**
     * 不適切なパスワードの長さかどうかを判定
     */
    public function invalidPasswordLength(string $password): bool
    {
        return mb_strlen($password, 'UTF-8') < self::MIN_LENGTH;
    }

    /**
     * パスワードの強度を検証
     */
    public function invalidPasswordStrength(string $password): bool
    {
        return !preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W_]).+$/', $password);
    }
}