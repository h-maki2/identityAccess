<?php

namespace packages\domain\model\userProfile\validation;

class UserPasswordValidation
{
    private const MIN_LENGTH = 8;

    public function handle(string $password): bool
    {
        if (mb_strlen($password, 'UTF-8') < self::MIN_LENGTH) {
            return false;
        }

        if (!$this->validatePasswordStrength($password)) {
            return false;
        }

        return true;
    }

    /**
     * パスワードの強度を検証
     * 適切な強度だったらtrue
     */
    private function validatePasswordStrength(string $password): bool
    {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W_]).+$/', $password);
    }
}