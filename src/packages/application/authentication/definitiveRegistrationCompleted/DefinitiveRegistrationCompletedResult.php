<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

class DefinitiveRegistrationCompletedUpdateResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;

    private function __construct(bool $validationError, string $validationErrorMessage)
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
    }

    public static function createWhenValidationError(string $validationErrorMessage): DefinitiveRegistrationCompletedUpdateResult
    {
        return new DefinitiveRegistrationCompletedUpdateResult(true, $validationErrorMessage);
    }

    public static function createWhenSuccess(): DefinitiveRegistrationCompletedUpdateResult
    {
        return new DefinitiveRegistrationCompletedUpdateResult(false, '');
    }
}