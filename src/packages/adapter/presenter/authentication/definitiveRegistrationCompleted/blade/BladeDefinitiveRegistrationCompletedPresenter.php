<?php

namespace packages\adapter\presenter\authentication\UserDefinitiveRegistration\blade;

use packages\application\authentication\UserDefinitiveRegistration\UserDefinitiveRegistrationResult;

class BladeUserDefinitiveRegistrationPresenter
{
    private UserDefinitiveRegistrationResult $UserDefinitiveRegistrationResult;

    public function __construct(UserDefinitiveRegistrationResult $UserDefinitiveRegistrationResult)
    {
        $this->UserDefinitiveRegistrationResult = $UserDefinitiveRegistrationResult;
    }

    public function responseDate(): array
    {
        if ($this->UserDefinitiveRegistrationResult->validationError) {
            return [
                'validationErrorMessage' => $this->UserDefinitiveRegistrationResult->validationErrorMessage
            ];
        }

        return [];
    }

    public function isValidationError(): bool
    {
        return $this->UserDefinitiveRegistrationResult->validationError;
    }
}