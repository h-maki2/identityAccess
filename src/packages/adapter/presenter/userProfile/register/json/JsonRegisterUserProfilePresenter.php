<?php

namespace packages\adapter\presenter\userProfile\register\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\application\userProfile\register\RegisterUserProfileOutputBoundary;
use packages\application\userProfile\register\RegisterUserProfileResult;

class JsonRegisterUserProfilePresenter implements JsonPresenter
{
    private RegisterUserProfileResult $registerUserProfileResult;

    public function __construct(RegisterUserProfileResult $registerUserProfileResult)
    {
        $this->registerUserProfileResult = $registerUserProfileResult;
    }

    public function jsonResponseData(): JsonResponseData
    {
        return new JsonResponseData($this->responseData(), $this->httpStatus());
    }

    private function responseData(): array
    {
        if ($this->registerUserProfileResult->isSuccess) {
            return [];
        }

        $responseData = [];
        foreach ($this->registerUserProfileResult->validationErrorMessageList as $validationErrorMessage) {
            $responseData[$validationErrorMessage->fieldName] = $validationErrorMessage->errorMessageList;
        }
        return $responseData;
    }

    private function httpStatus(): HttpStatus
    {
        return $this->registerUserProfileResult->isSuccess ? HttpStatus::Success : HttpStatus::BadRequest;
    }
}