<?php

namespace packages\application\authentication\definitiveRegistrationCompleted;

class DefinitiveRegistrationCompleteResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;

    private function __construct(bool $validationError, string $validationErrorMessage)
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
    }

    public static function createWhenValidationError(string $validationErrorMessage): DefinitiveRegistrationCompleteResult
    {
        return new DefinitiveRegistrationCompleteResult(true, $validationErrorMessage);
    }

    public static function createWhenSuccess(): DefinitiveRegistrationCompleteResult
    {
        return new DefinitiveRegistrationCompleteResult(false, '');
    }
}