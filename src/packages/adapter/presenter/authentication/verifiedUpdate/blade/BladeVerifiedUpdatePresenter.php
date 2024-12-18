<?php

namespace packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade;

use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedResult;

class BladeDefinitiveRegistrationCompletedPresenter
{
    private DefinitiveRegistrationCompletedResult $DefinitiveRegistrationCompletedResult;

    public function __construct(DefinitiveRegistrationCompletedResult $DefinitiveRegistrationCompletedResult)
    {
        $this->DefinitiveRegistrationCompletedResult = $DefinitiveRegistrationCompletedResult;
    }

    public function responseDate(): array
    {
        if ($this->DefinitiveRegistrationCompletedResult->validationError) {
            return [
                'validationErrorMessage' => $this->DefinitiveRegistrationCompletedResult->validationErrorMessage
            ];
        }

        return [];
    }

    public function isValidationError(): bool
    {
        return $this->DefinitiveRegistrationCompletedResult->validationError;
    }
}