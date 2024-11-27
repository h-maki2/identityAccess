<?php

namespace packages\adapter\presenter\authentication\userRegistration\json;

use packages\application\userRegistration\UserRegistrationOutputBoundary;
use packages\application\userRegistration\UserRegistrationResult;

class JsonUserRegistrationPresenter implements UserRegistrationOutputBoundary
{
    private array $responseData;
    private int $statusCode;

    public function formatForResponse(UserRegistrationResult $result): void
    {
        $this->setResponseData($result);
        $this->setStatusCode($result);
    }

    public function response(): mixed
    {
        return response()->json($this->responseData, $this->statusCode);
    }

    private function setResponseData(UserRegistrationResult $result): void
    {
        $this->responseData = [
            'validationErrorMessageList' => $result->validationErrorMessageList
        ];
    }

    private function setStatusCode(UserRegistrationResult $result): void
    {
        $this->statusCode = $result->validationError ? 400 : 200;
    }
}