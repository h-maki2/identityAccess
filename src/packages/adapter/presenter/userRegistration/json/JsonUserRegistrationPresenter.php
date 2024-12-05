<?php

namespace packages\adapter\presenter\userRegistration\json;

use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\userRegistration\UserRegistrationOutputBoundary;
use packages\application\userRegistration\UserRegistrationResult;

class JsonUserRegistrationPresenter extends JsonPresenter implements UserRegistrationOutputBoundary
{
    private array $responseData;
    private int $statusCode;
    private JsonResponseStatus $jsonResponseStatus;

    public function formatForResponse(UserRegistrationResult $result): void
    {
        $this->setResponseData($result);
        $this->setHttpStatusCode($result);
        $this->setJsonResponseStatus($result);
    }

    public function response(): mixed
    {
        return $this->jsonResponse($this->responseData, $this->jsonResponseStatus, $this->statusCode);
    }

    private function setResponseData(UserRegistrationResult $result): void
    {
        $this->responseData = [
            'validationErrorMessageList' => $result->validationErrorMessageList
        ];
    }

    private function setHttpStatusCode(UserRegistrationResult $result): void
    {
        $this->statusCode = $result->validationError ? 400 : 200;
    }

    private function setJsonResponseStatus(UserRegistrationResult $result): void
    {
        $this->jsonResponseStatus = $result->validationError ? JsonResponseStatus::ValidationError : JsonResponseStatus::Success;
    }
}