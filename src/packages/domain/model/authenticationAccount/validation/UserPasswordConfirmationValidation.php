<?php

namespace packages\domain\model\authenticationAccount\validation;

use packages\domain\model\common\validator\Validator;

class UserPasswordConfirmationValidation extends Validator
{
    private string $password;
    private string $passwordConfirmation;

    public function __construct(string $password, string $passwordConfirmation)
    {
        $this->password = $password;
        $this->passwordConfirmation = $passwordConfirmation;
    }

    public function validate(): bool
    {
        if ($this->password !== $this->passwordConfirmation) {
            $this->setErrorMessage('パスワードが一致しません。');
            return false;
        }

        return true;
    }

    public function fieldName(): string
    {
        return 'passwordConfirmation';
    }
}