<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

class DefinitiveRegistrationConfirmedUpdateResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;

    private function __construct(bool $validationError, string $validationErrorMessage)
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
    }

    public static function createWhenValidationError(string $validationErrorMessage): DefinitiveRegistrationConfirmedUpdateResult
    {
        return new DefinitiveRegistrationConfirmedUpdateResult(true, $validationErrorMessage);
    }

    public static function createWhenSuccess(): DefinitiveRegistrationConfirmedUpdateResult
    {
        return new DefinitiveRegistrationConfirmedUpdateResult(false, '');
    }
}