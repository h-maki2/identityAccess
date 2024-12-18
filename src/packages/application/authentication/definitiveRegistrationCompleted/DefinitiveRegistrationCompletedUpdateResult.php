<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

class DefinitiveRegistrationCompleteUpdateResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;

    private function __construct(bool $validationError, string $validationErrorMessage)
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
    }

    public static function createWhenValidationError(string $validationErrorMessage): DefinitiveRegistrationCompleteUpdateResult
    {
        return new DefinitiveRegistrationCompleteUpdateResult(true, $validationErrorMessage);
    }

    public static function createWhenSuccess(): DefinitiveRegistrationCompleteUpdateResult
    {
        return new DefinitiveRegistrationCompleteUpdateResult(false, '');
    }
}