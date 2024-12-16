<?php

namespace packages\adapter\presenter\userRegistration\json;

use App\Models\User;
use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\adapter\presenter\userRegistration\UserRegistrationPresenter;
use packages\application\common\validation\ValidationErrorMessageData;
use packages\application\userRegistration\UserRegistrationResult;
use UserRegistrationView;

class JsonUserRegistrationPresenter
{
    private UserRegistrationResult $result;

    public function __construct(UserRegistrationResult $result)
    {
        $this->result = $result;
    }

    public function jsonResponseData(): JsonResponseData
    {
        return new JsonResponseData($this->responseData(), $this->setHttpStatusToView());
    }

    protected function responseData(): array
    {
        if (!$this->result->isValidationError) {
            return [];
        }

        $responseData = [];
        foreach ($this->validationErrorMessageList() as $validationError) {
            $responseData[$validationError->fieldName] = $validationError->errorMessageList;
        }
        return $responseData;
    }

    protected function setHttpStatusToView(): HttpStatus
    {
        return $this->result->isValidationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }

    /**
     * @return ValidationErrorMessageData[]
     */
    protected function validationErrorMessageList(): array
    {
        return $this->result->validationErrors;
    }
}