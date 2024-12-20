<?php

namespace packages\application\registration\definitiveRegistration;

class DefinitiveRegistrationResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;

    private function __construct(bool $validationError, string $validationErrorMessage)
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
    }

    public static function createWhenValidationError(string $validationErrorMessage): DefinitiveRegistrationResult
    {
        return new DefinitiveRegistrationResult(true, $validationErrorMessage);
    }

    public static function createWhenSuccess(): DefinitiveRegistrationResult
    {
        return new DefinitiveRegistrationResult(false, '');
    }
}