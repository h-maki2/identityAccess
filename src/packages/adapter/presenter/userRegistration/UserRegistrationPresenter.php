<?php

namespace packages\adapter\presenter\userRegistration;

use packages\application\userRegistration\UserRegistrationResult;

abstract class UserRegistrationPresenter
{
    protected UserRegistrationResult $result;

    public function __construct(UserRegistrationResult $result)
    {
        $this->result = $result;
    }

    abstract public function viewResponse(): mixed;

    protected function responseData(): array
    {
        if (!$this->result->validationError) {
            return [];
        }

        $responseData = [];
        foreach ($this->result->validationErrorMessageList as $validationError) {
            $responseData[$validationError->fieldName] = $validationError->errorMessageList;
        }
        return $responseData;
    }
}