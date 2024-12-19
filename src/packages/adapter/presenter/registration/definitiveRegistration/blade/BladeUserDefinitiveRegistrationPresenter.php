<?php

namespace packages\adapter\presenter\registration\definitiveRegistration\blade;

use packages\application\registration\definitiveRegistration\UserDefinitiveRegistrationResult;

class BladeUserDefinitiveRegistrationPresenter
{
    private UserDefinitiveRegistrationResult $result;

    public function __construct(UserDefinitiveRegistrationResult $result)
    {
        $this->result = $result;
    }

    public function responseDate(): array
    {
        if ($this->result->validationError) {
            return [
                'validationErrorMessage' => $this->result->validationErrorMessage
            ];
        }

        return [];
    }

    public function isValidationError(): bool
    {
        return $this->result->validationError;
    }
}