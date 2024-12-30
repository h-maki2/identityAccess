<?php

namespace packages\application\changePassword;

class ChangePasswordResult
{
    readonly array $validationErrorMessageList;
    readonly bool $isValidationError;

    private function __construct(
        array $validationErrorMessageList,
        bool $isValidationError
    )
    {
        $this->validationErrorMessageList = $validationErrorMessageList;
        $this->isValidationError = $isValidationError;
    }

    public static function createWhenFaild(array $validationErrorMessageList): self
    {
        return new self($validationErrorMessageList, true);
    }

    public static function createWhenSuccess(): self
    {
        return new self([], false);
    }
}