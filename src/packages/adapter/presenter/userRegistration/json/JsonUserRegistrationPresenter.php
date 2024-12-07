<?php

namespace packages\adapter\presenter\userRegistration\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\userRegistration\UserRegistrationOutputBoundary;
use packages\application\userRegistration\UserRegistrationResult;

class JsonUserRegistrationPresenter extends JsonPresenter implements UserRegistrationOutputBoundary
{
    private array $responseData;
    private HttpStatus $httpStatus;

    public function formatForResponse(UserRegistrationResult $result): void
    {
        $this->setResponseData($result);
        $this->setHttpStatusCode($result);
    }

    public function response(): mixed
    {
        return $this->jsonResponse($this->responseData, $this->httpStatus);
    }

    private function setResponseData(UserRegistrationResult $result): void
    {
        if ($result->validationError) {
            $this->responseData = [
                'validationErrorMessageList' => $result->validationErrorMessageList
            ];
            return;
        }
        
        $this->responseData = [];
    }

    private function setHttpStatusCode(UserRegistrationResult $result): void
    {
        $this->httpStatus = $result->validationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}