<?php

namespace packages\application\userProfile\register;

class RegisterUserProfileResult
{
    readonly bool $isSuccess;
    readonly array $validationErrorMessageList;

    /**
     * @param ValidationErrorMessageData[] $validationErrorMessageList
     */
    private function __construct(bool $isSucess, array $validationErrorMessageList)
    {
        $this->isSuccess = $isSucess;
        $this->validationErrorMessageList = $validationErrorMessageList;
    }

    public static function createWhenSuccess(): self
    {
        return new self(true, []);
    }

    /**
     * @param ValidationErrorMessageData[] $validationErrorMessageList
     */
    public static function createWhenFailure(array $validationErrorMessageList): self
    {
        return new self(false, $validationErrorMessageList);
    }
}