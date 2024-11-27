<?php

namespace packages\application\authentication\ResendRegistrationConfirmationEmail;

class ResendRegistrationConfirmationEmailResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;
    readonly string $oneTimeTokenValue;

    private function __construct(
        bool $validationError, 
        string $validationErrorMessage,
        string $oneTimeTokenValue
    )
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
        $this->oneTimeTokenValue = $oneTimeTokenValue;
    }

    public static function createWhenValidationError(string $validationErrorMessage): self
    {
        return new self(true, $validationErrorMessage, '');
    }

    public static function createWhenSuccess(string $oneTimeTokenValue): self
    {
        return new self(false, '', $oneTimeTokenValue);
    }
}