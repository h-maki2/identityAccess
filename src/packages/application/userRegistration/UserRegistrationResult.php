<?php

namespace packages\application\userRegistration;

class UserRegistrationResult
{
    readonly bool $validationError;
    readonly array $validationErrorMessageList; // ValidationErrorMessageData[]
    readonly bool $isSuccess;
    readonly string $oneTimeToken;

    /**
     * @param ValidationErrorMessageData[] $validationErrorMessageList
     */
    private function __construct(
        bool $validationError,
        array $validationErrorMessageList,
        bool $isSuccess,
        string $oneTimeToken
    )
    {
        $this->validationError = $validationError;
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
            $validationErrorMessageList, 
            false,
            ''
        );
    }

    public static function createWhenSuccess(string $oneTimeToken): self
    {
        return new self(
            false,
            [], 
            true,
            $oneTimeToken
        );
    }
}