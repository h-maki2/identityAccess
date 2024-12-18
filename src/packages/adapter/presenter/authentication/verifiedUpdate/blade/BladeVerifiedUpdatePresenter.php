<?php

namespace packages\adapter\presenter\authentication\definitiveRegistrationCompleted\blade;

use packages\application\authentication\definitiveRegistrationCompleted\DefinitiveRegistrationCompleteResult;

class BladeDefinitiveRegistrationCompletedUpdatePresenter
{
    private DefinitiveRegistrationCompleteResult $DefinitiveRegistrationCompleteResult;

    public function __construct(DefinitiveRegistrationCompleteResult $DefinitiveRegistrationCompleteResult)
    {
        $this->DefinitiveRegistrationCompleteResult = $DefinitiveRegistrationCompleteResult;
    }

    public function responseDate(): array
    {
        if ($this->DefinitiveRegistrationCompleteResult->validationError) {
            return [
                'validationErrorMessage' => $this->DefinitiveRegistrationCompleteResult->validationErrorMessage
            ];
        }

        return [];
    }

    public function isValidationError(): bool
    {
        return $this->DefinitiveRegistrationCompleteResult->validationError;
    }
}