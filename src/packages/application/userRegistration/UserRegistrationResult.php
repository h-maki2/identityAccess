<?php

namespace packages\application\userRegistration;

use packages\application\common\validation\ValidationErrorMessageData;

class UserRegistrationResult
{
    readonly bool $validationError;
    readonly array $validationErrorMessageList; // ValidationErrorMessageData[]
    readonly string $email;
    readonly string $password;
    readonly string $passwordConfirmation;

    /**
     * @param ValidationErrorMessageData[] $validationErrorMessageList
     */
    private function __construct(
        bool $validationError,
        array $validationErrorMessageList,
        string $email,
        string $password,
        string $passwordConfirmation
    )
    {
        $this->validationError = $validationError;
        $this->validationErrorMessageList = $validationErrorMessageList;
        $this->email = $email;
        $this->password = $password;
        $this->passwordConfirmation = $passwordConfirmation;
    }

    /**
     * @param ValidationErrorMessageData[] $validationErrorMessageList
     */
    public static function createWhenValidationError(
        array $validationErrorMessageList,
        string $email,
        string $password,
        string $passwordConfirmation
    ): self
    {
        return new self(
            true,
            $validationErrorMessageList,
            $email,
            $password,
            $passwordConfirmation
        );
    }

    public static function createWhenSuccess(): self
    {
        return new self(
            false,
            [],
            '',
            '',
            ''
        );
    }
}