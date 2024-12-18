<?php

namespace packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade;

use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedResult;

class BladeDefinitiveRegistrationCompletedPresenter
{
    private DefinitiveRegistrationCompletedResult $definitiveRegistrationCompletedResult;

    public function __construct(DefinitiveRegistrationCompletedResult $definitiveRegistrationCompletedResult)
    {
        $this->definitiveRegistrationCompletedResult = $definitiveRegistrationCompletedResult;
    }

    public function responseDate(): array
    {
        if ($this->definitiveRegistrationCompletedResult->validationError) {
            return [
                'validationErrorMessage' => $this->definitiveRegistrationCompletedResult->validationErrorMessage
            ];
        }

        return [];
    }

    public function isValidationError(): bool
    {
        return $this->definitiveRegistrationCompletedResult->validationError;
    }
}