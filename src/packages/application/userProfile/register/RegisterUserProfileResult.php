<?php

namespace packages\application\userProfile\register;

class RegisterUserProfileResult
{
    readonly bool $isSucess;
    readonly array $validationErrorMessageList;

    /**
     * @param ValidationErrorMessageData[] $validationErrorMessageList
     */
    private function __construct(bool $isSucess, array $validationErrorMessageList)
    {
        $this->isSucess = $isSucess;
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