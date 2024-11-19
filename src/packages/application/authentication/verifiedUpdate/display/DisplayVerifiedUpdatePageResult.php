<?php

namespace packages\domain\model\authConfirmation;

class DisplayVerifiedUpdatePageResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;
    readonly string $oneTimeTokenValue;
    readonly string $oneTimePassword;

    private function __construct(
        bool $validationError,
        string $validationErrorMessage,
        string $oneTimeTokenValue,
        string $oneTimePassword
    )
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
        $this->oneTimeTokenValue = $oneTimeTokenValue;
        $this->oneTimePassword = $oneTimePassword;
    }

    public static function createWhenValidationError(string $validationErrorMessage): self
    {
        return new self(true, $validationErrorMessage, '', '');
    }

    public static function createWhenSuccess(
        OneTimeTokenValue $oneTimeTokenValue,
        OneTimePassword $oneTimePassword
    ): self
    {
        return new self(false, '', $oneTimeTokenValue->value, $oneTimePassword->value);
    }
}