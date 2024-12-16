<?php

namespace packages\adapter\presenter\userRegistration\blade;

use packages\application\userRegistration\UserRegistrationResult;

class BladeUserRegistrationPresenter
{
    private UserRegistrationResult $result;

    public function __construct(UserRegistrationResult $result)
    {
        $this->result = $result;
    }

    public function viewResponse(): BladeUserRegistrationViewModel
    {
        return new BladeUserRegistrationViewModel(
            $this->responseData(), 
            $this->result->validationError
        );
    }

    private function responseData(): array
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