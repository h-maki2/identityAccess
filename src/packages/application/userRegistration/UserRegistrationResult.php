<?php

namespace packages\application\userRegistration;

use packages\application\common\validation\ValidationErrorMessageData;

class UserRegistrationResult
{
    readonly bool $isValidationError;
    readonly mixed $validationError;

    private function __construct(
        bool $isValidationError,
        mixed $validationError
    )
    {
        $this->isValidationError= $isValidationError;
        $this->validationError = $validationError;
    }

    public static function createWhenValidationError(
        mixed $validationError
    ): self
    {
        return new self(
            true,
            $validationError
        );
    }

    public static function createWhenSuccess(): self
    {
        return new self(
            false,
            ''
        );
    }
}