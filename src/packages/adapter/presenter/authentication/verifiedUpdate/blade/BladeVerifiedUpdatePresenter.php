<?php

namespace packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade;

use packages\application\authentication\definitiveRegistrationCompleted\definitiveRegistrationConfirmedUpdateResult;

class BladeDefinitiveRegistrationConfirmedUpdatePresenter
{
    private DefinitiveRegistrationConfirmedUpdateResult $definitiveRegistrationConfirmedUpdateResult;

    public function __construct(DefinitiveRegistrationConfirmedUpdateResult $definitiveRegistrationConfirmedUpdateResult)
    {
        $this->definitiveRegistrationConfirmedUpdateResult = $definitiveRegistrationConfirmedUpdateResult;
    }

    public function responseDate(): array
    {
        if ($this->definitiveRegistrationConfirmedUpdateResult->validationError) {
            return [
                'validationErrorMessage' => $this->definitiveRegistrationConfirmedUpdateResult->validationErrorMessage
            ];
        }

        return [];
    }

    public function isValidationError(): bool
    {
        return $this->definitiveRegistrationConfirmedUpdateResult->validationError;
    }
}