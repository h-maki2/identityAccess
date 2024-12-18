<?php

namespace packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade;

use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompleteUpdateResult;

class BladeDefinitiveRegistrationCompletedUpdatePresenter
{
    private DefinitiveRegistrationCompleteUpdateResult $DefinitiveRegistrationCompleteUpdateResult;

    public function __construct(DefinitiveRegistrationCompleteUpdateResult $DefinitiveRegistrationCompleteUpdateResult)
    {
        $this->DefinitiveRegistrationCompleteUpdateResult = $DefinitiveRegistrationCompleteUpdateResult;
    }

    public function responseDate(): array
    {
        if ($this->DefinitiveRegistrationCompleteUpdateResult->validationError) {
            return [
                'validationErrorMessage' => $this->DefinitiveRegistrationCompleteUpdateResult->validationErrorMessage
            ];
        }

        return [];
    }

    public function isValidationError(): bool
    {
        return $this->DefinitiveRegistrationCompleteUpdateResult->validationError;
    }
}