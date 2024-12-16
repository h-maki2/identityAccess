<?php

namespace packages\application\userRegistration;

use packages\application\common\validation\ValidationErrorMessageData;

class UserRegistrationResult
{
    readonly bool $isValidationError;
    readonly mixed $validationErrors;

    private function __construct(
        bool $isValidationError,
        mixed $validationErrors
    )
    {
        $this->isValidationError= $isValidationError;
        $this->validationErrors = $validationErrors;
    }

    public static function createWhenValidationError(
        mixed $validationErrors
    ): self
    {
        return new self(
            true,
            $validationErrors
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