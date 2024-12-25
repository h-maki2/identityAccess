<?php

namespace packages\application\changePassword;

class ChangePasswordResult
{
    readonly array $validationErrorMessageList;
    readonly bool $isValidationError;
    readonly string $redirectUrl;

    private function __construct(
        array $validationErrorMessageList,
        bool $isValidationError,
        string $redirectUrl
    )
    {
        $this->validationErrorMessageList = $validationErrorMessageList;
        $this->isValidationError = $isValidationError;
        $this->redirectUrl = $redirectUrl;
    }

    public static function createWhenFaild(array $validationErrorMessageList): self
    {
        return new self($validationErrorMessageList, true, '');
    }

    public static function createWhenSuccess(string $redirectUrl): self
    {
        return new self([], false, $redirectUrl);
    }
}