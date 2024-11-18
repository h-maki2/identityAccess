<?php

namespace packages\application\userRegistration;

class UserRegistrationResult
{
    readonly bool $validationError;
    readonly bool $transactionError;
    readonly array $validationErrorMessageList; // ValidationErrorMessageData[]
    readonly bool $isSuccess;
    readonly string $oneTimeToken;

    /**
     * @param ValidationErrorMessageData[] $validationErrorMessageList
     */
    private function __construct(
        bool $validationError,
        bool $transactionError,
        array $validationErrorMessageList,
        bool $isSuccess,
        string $oneTimeToken
    )
    {
        $this->validationError = $validationError;
        $this->transactionError = $transactionError;
        $this->validationErrorMessageList = $validationErrorMessageList;
        $this->isSuccess = $isSuccess;
        $this->oneTimeToken = $oneTimeToken;
    }

    /**
     * @param ValidationErrorMessageData[] $validationErrorMessageList
     */
    public static function createWhenValidationError(
        array $validationErrorMessageList
    ): self
    {
        return new self(
            true, 
            false, 
            $validationErrorMessageList, 
            false,
            ''
        );
    }

    public static function createWhenTransactionError(): self
    {
        return new self(
            false, 
            true, 
            [], 
            false,
            ''
        );
    }

    public static function createWhenSuccess(string $oneTimeToken): self
    {
        return new self(
            false, 
            false, 
            [], 
            true,
            $oneTimeToken
        );
    }
}