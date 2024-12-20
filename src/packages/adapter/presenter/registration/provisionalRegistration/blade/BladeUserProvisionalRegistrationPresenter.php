<?php

namespace packages\adapter\presenter\registration\provisionalRegistration\blade;

use packages\application\registration\provisionalRegistration\ProvisionalRegistrationResult;

class BladeProvisionalRegistrationPresenter
{
    private ProvisionalRegistrationResult $result;

    public function __construct(ProvisionalRegistrationResult $result)
    {
        $this->result = $result;
    }

    public function viewResponse(): BladeProvisionalRegistrationViewModel
    {
        return new BladeProvisionalRegistrationViewModel(
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