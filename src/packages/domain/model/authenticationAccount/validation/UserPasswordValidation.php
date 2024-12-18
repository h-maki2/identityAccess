<?php

namespace packages\domain\model\authenticationAccount\validation;

use packages\domain\model\common\validator\Validator;

class UserPasswordValidation extends Validator
{
    private const MIN_LENGTH = 8;

    private string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function validate(): bool
    {
        $valid = true;

        if ($this->invalidPasswordLength()) {
            $this->setErrorMessage('パスワードは8文字以上で入力してください');
            $valid = false;
        }

        if ($this->invalidPasswordStrength()) {
            $this->setErrorMessage('パスワードは大文字、小文字、数字、記号をそれぞれ1文字以上含めてください');
            $valid = false;
        }

        return $valid;
    }
    
    public function fieldName(): string
    {
        return 'password';
    }

    /**
     * 不適切なパスワードの長さかどうかを判定
     */
    protected function invalidPasswordLength(): bool
    {
        return mb_strlen($this->password, 'UTF-8') < self::MIN_LENGTH;
    }

    /**
     * パスワードの強度を検証
     */
    protected function invalidPasswordStrength(): bool
    {
        return !preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W_]).+$/', $this->password);
    }
}