<?php

namespace packages\adapter\presenter\registration\resendDefinitiveRegistrationConfirmation\blade;

use packages\application\registration\resendDefinitiveRegistrationConfirmation\ResendDefinitiveRegistrationConfirmationResult;

class BladeResendDefinitiveRegistrationConfirmationPresenter
{
    private ResendDefinitiveRegistrationConfirmationResult $result;

    public function __construct(ResendDefinitiveRegistrationConfirmationResult $result)
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