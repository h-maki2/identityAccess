<?php

namespace packages\application\userRegistration;

class UserRegistrationResult
{
    readonly bool $validationError;
    readonly array $validationErrorMessageList; // ValidationErrorMessageData[]
    readonly string $oneTimeToken;

    /**
     * @param ValidationErrorMessageData[] $validationErrorMessageList
     */
    private function __construct(
        bool $validationError,
        array $validationErrorMessageList,
        string $oneTimeToken
    )
    {
        $this->validationError = $validationError;
        $this->validationErrorMessageList = $validationErrorMessageList;
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
            ''
        );
    }

    public static function createWhenSuccess(string $oneTimeToken): self
    {
        return new self(
            false,
            [],
            $oneTimeToken
        );
    }
}