<?php

namespace packages\application\registration\userProvisionalRegistration;

use packages\application\common\validation\ValidationErrorMessageData;

class UserProvisionalRegistrationResult
{
    readonly bool $validationError;
    readonly array $validationErrorMessageList; // ValidationErrorMessageData[]

    /**
     * @param ValidationErrorMessageData[] $validationErrorMessageList
     */
    private function __construct(
        bool $validationError,
        array $validationErrorMessageList
    )
    {
        $this->validationError = $validationError;
        $this->validationErrorMessageList = $validationErrorMessageList;
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
            $validationErrorMessageList
        );
    }

    public static function createWhenSuccess(): self
    {
        return new self(
            false,
            []
        );
    }
}