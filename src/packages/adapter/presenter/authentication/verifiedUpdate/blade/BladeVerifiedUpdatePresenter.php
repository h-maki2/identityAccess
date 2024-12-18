<?php

namespace packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade;

use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompletedUpdateResult;

class BladeDefinitiveRegistrationCompletedUpdatePresenter
{
    private DefinitiveRegistrationCompletedUpdateResult $DefinitiveRegistrationCompletedUpdateResult;

    public function __construct(DefinitiveRegistrationCompletedUpdateResult $DefinitiveRegistrationCompletedUpdateResult)
    {
        $this->handleResult = $DefinitiveRegistrationCompletedUpdateResult;
    }

    public function responseDate(): array
    {
        if ($this->handleResult->validationError) {
            return [
                'validationErrorMessage' => $this->handleResult->validationErrorMessage
            ];
        }

        return [];
    }

    public function isValidationError(): bool
    {
        return $this->handleResult->validationError;
    }
}