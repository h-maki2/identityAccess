<?php

namespace packages\adapter\presenter\registration\definitiveRegistration\blade;

// packages\adapter\presenter\registration\definitiveRegistration\blade

use packages\application\registration\definitiveRegistration\UserDefinitiveRegistrationResult;

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