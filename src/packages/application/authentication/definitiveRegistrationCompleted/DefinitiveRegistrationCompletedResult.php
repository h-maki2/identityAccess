<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

class DefinitiveRegistrationCompletedResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;

    private function __construct(bool $validationError, string $validationErrorMessage)
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
    }

    public static function createWhenValidationError(string $validationErrorMessage): DefinitiveRegistrationCompletedResult
    {
        return new DefinitiveRegistrationCompletedResult(true, $validationErrorMessage);
    }

    public static function createWhenSuccess(): DefinitiveRegistrationCompletedResult
    {
        return new DefinitiveRegistrationCompletedResult(false, '');
    }
}