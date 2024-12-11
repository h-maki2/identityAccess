<?php

namespace packages\adapter\presenter\userRegistration\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\userRegistration\UserRegistrationOutputBoundary;
use packages\application\userRegistration\UserRegistrationResult;

class JsonUserRegistrationPresenter
{
    private UserRegistrationResult $result;

    public function __construct(UserRegistrationResult $result)
    {
        $this->result = $result;
    }

    public function jsonResponseData(): JsonPresenter
    {
        return new JsonPresenter($this->responseData($this->result), $this->httpStatus());
    }

    private function responseData(UserRegistrationResult $result): array
    {
        if (!$result->validationError) {
            return [];
        }

        $responseData = [];
        foreach ($result->validationErrorMessageList as $validationError) {
            $responseData[$validationError->fieldName] = $validationError->errorMessageList;
        }
        return $responseData;
    }

    private function httpStatus(): HttpStatus
    {
        return $this->result->validationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}