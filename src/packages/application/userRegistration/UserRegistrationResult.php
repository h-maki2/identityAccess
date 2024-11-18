<?php

namespace packages\application\userRegistration;

class UserRegistrationResult
{
    readonly bool $validationError;
    readonly bool $transactionError;
    readonly array $validationErrorMessageList;
    readonly bool $isSuccess;

    private function __construct(
        bool $validationError,
        bool $transactionError,
        array $validationErrorMessageList,
        bool $isSuccess
    )
    {
        $this->validationError = $validationError;
        $this->transactionError = $transactionError;
        $this->validationErrorMessageList = $validationErrorMessageList;
        $this->isSuccess = $isSuccess;
    }

    public static function createWhenValidationError(
        array $validationErrorMessageList
    ): self
    {
        return new self(true, false, $validationErrorMessageList, false);
    }

    public static function createWhenTransactionError(): self
    {
        return new self(false, true, [], false);
    }

    public static function createWhenSuccess(): self
    {
        return new self(false, false, [], true);
    }
}