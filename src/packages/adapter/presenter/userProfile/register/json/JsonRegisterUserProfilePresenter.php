<?php

namespace packages\adapter\presenter\userProfile\register\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\application\userProfile\register\RegisterUserProfileOutputBoundary;
use packages\application\userProfile\register\RegisterUserProfileResult;

class JsonRegisterUserProfilePresenter extends JsonPresenter implements RegisterUserProfileOutputBoundary
{
    private array $responseData;
    private HttpStatus $httpStatus;

    public function formatForResponse(RegisterUserProfileResult $registerUserProfileResult): void
    {
        $this->setResponseData($registerUserProfileResult);
        $this->setHttpStatusCode($registerUserProfileResult);
    }

    public function response(): mixed
    {
        return $this->jsonResponse($this->responseData, $this->httpStatus);
    }

    private function setResponseData(RegisterUserProfileResult $registerUserProfileResult): void
    {
        if ($registerUserProfileResult->isSucess) {
            $this->responseData = [];
            return;
        }


        foreach ($registerUserProfileResult->validationErrorMessageList as $validationErrorMessage) {
            $this->responseData[$validationErrorMessage->fieldName] = $validationErrorMessage->errorMessageList;
        }
    }

    private function setHttpStatusCode(RegisterUserProfileResult $registerUserProfileResult): void
    {
        $this->httpStatus = $registerUserProfileResult->isSucess ? HttpStatus::Success : HttpStatus::BadRequest;
    }
}