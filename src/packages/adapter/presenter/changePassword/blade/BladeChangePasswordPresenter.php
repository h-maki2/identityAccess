<?php

namespace packages\adapter\presenter\changePassword\blade;

use packages\application\changePassword\change\ChangePasswordResult;

class BladeChangePasswordPresenter
{
    private ChangePasswordResult $result;

    public function __construct(ChangePasswordResult $result)
    {
        $this->result = $result;
    }

    public function isValidationError(): bool
    {
        return $this->result->isValidationError;
    }

    public function successResponseData(): string
    {
        return $this->result->redirectUrl;
    }

    public function faildResponseData(): array
    {
        return [
            'validationErrorMessageList' => $this->result->validationErrorMessageList
        ];
    }
}