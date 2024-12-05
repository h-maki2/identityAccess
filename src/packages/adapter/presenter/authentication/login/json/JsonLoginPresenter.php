<?php

namespace packages\adapter\presenter\authentication\login\json;

use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\authentication\login\LoginOutputBoundary;
use packages\application\authentication\login\LoginResult;

class JsonLoginPresenter extends JsonPresenter implements LoginOutputBoundary
{
    private array $responseData;
    private int $statusCode;
    private JsonResponseStatus $jsonResponseStatus;

    public function formatForResponse(LoginResult $loginResult): void
    {
        $this->setResponseData($loginResult);
        $this->setStatusCode($loginResult);
        $this->setJsonResponseStatus($loginResult);
    }

    public function response(): mixed
    {
        return $this->jsonResponse($this->responseData, $this->jsonResponseStatus, $this->statusCode);
    }

    private function setResponseData(LoginResult $loginResult): void
    {
        $this->responseData = [
            'authorizationUrl' => $loginResult->authorizationUrl,
            'loginSucceeded' => $loginResult->loginSucceeded,
            'accountLocked' => $loginResult->accountLocked
        ];
    }

    private function setStatusCode(LoginResult $loginResult): void
    {
        $this->statusCode = $loginResult->loginSucceeded ? 200 : 400;
    }

    private function setJsonResponseStatus(LoginResult $loginResult): void
    {
        $this->jsonResponseStatus = $loginResult->loginSucceeded ? JsonResponseStatus::Success : JsonResponseStatus::AuthenticationError;
    }
}