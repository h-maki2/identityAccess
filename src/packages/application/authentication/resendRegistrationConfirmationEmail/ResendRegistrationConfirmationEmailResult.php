<?php

namespace packages\application\authentication\resendRegistrationConfirmationEmail;

class ResendRegistrationConfirmationEmailResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;

    private function __construct(
        bool $validationError, 
        string $validationErrorMessage
    )
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
    }

    public static function createWhenValidationError(string $validationErrorMessage): self
    {
        return new self(true, $validationErrorMessage);
    }

    public static function createWhenSuccess(): self
    {
        return new self(false, '',);
    }
}