<?php

namespace packages\application\registration\definitiveRegistration;

class UserDefinitiveRegistrationResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;

    private function __construct(bool $validationError, string $validationErrorMessage)
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
    }

    public static function createWhenValidationError(string $validationErrorMessage): UserDefinitiveRegistrationResult
    {
        return new UserDefinitiveRegistrationResult(true, $validationErrorMessage);
    }

    public static function createWhenSuccess(): UserDefinitiveRegistrationResult
    {
        return new UserDefinitiveRegistrationResult(false, '');
    }
}