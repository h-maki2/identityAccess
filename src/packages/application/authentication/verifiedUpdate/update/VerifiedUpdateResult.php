<?php

namespace packages\application\authentication\verifiedUpdate\update;

class VerifiedUpdateResult
{
    readonly bool $validationError;
    readonly string $validationErrorMessage;

    private function __construct(bool $validationError, string $validationErrorMessage)
    {
        $this->validationError = $validationError;
        $this->validationErrorMessage = $validationErrorMessage;
    }

    public static function createWhenValidationError(string $validationErrorMessage): VerifiedUpdateResult
    {
        return new VerifiedUpdateResult(true, $validationErrorMessage);
    }

    public static function createWhenSuccess(): VerifiedUpdateResult
    {
        return new VerifiedUpdateResult(false, '');
    }
}