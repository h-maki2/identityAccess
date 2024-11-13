<?php

namespace packages\domain\model\authenticationInformaion\validation;

use packages\domain\model\common\validator\Validator;

class UserEmailValidation extends Validator
{
    private const MIN_LENGTH = 255;

    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function validate(): bool
    {
        if ($this->invalidLength() || $this->invalidEmail()) {
            $this->setErrorMessage('不正なメールアドレスです。');
            return false;
        }

        return true;
    }

    protected function invalidLength(): bool
    {
        if (empty($this->email)) {
            return true;
        }

        return mb_strlen($this->email, 'UTF-8') > self::MIN_LENGTH;
    }

    protected function invalidEmail(): bool
    {
        return !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $this->email);
    }

    protected function fieldName(): string
    {
        return 'email';
    }
}