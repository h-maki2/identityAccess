<?php

namespace packages\adapter\presenter\UserProvisionalRegistration\blade;

use packages\application\registration\userProvisionalRegistration\UserProvisionalRegistrationResult;

class BladeUserProvisionalRegistrationPresenter
{
    private UserProvisionalRegistrationResult $result;

    public function __construct(UserProvisionalRegistrationResult $result)
    {
        $this->result = $result;
    }

    public function viewResponse(): BladeUserProvisionalRegistrationViewModel
    {
        return new BladeUserProvisionalRegistrationViewModel(
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