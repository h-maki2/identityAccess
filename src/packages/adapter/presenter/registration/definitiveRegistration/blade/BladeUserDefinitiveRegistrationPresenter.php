<?php

namespace packages\adapter\presenter\registration\definitiveRegistration\blade;

use packages\application\registration\definitiveRegistration\DefinitiveRegistrationResult;

class BladeDefinitiveRegistrationPresenter
{
    private DefinitiveRegistrationResult $result;

    public function __construct(DefinitiveRegistrationResult $result)
    {
        $this->result = $result;
    }

    public function responseData(): array
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