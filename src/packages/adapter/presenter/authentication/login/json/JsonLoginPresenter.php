<?php

namespace packages\adapter\presenter\authentication\login\json;

use packages\application\authentication\login\LoginOutputBoundary;
use packages\application\authentication\login\LoginResult;

class JsonLoginPresenter implements LoginOutputBoundary
{
    private array $responseData;
    private int $statusCode;

    public function formatForResponse(LoginResult $loginResult): void
    {
        $this->setResponseData($loginResult);
        $this->setStatusCode($loginResult);
    }

    public function response(): mixed
    {
        return response()->json($this->responseData, $this->statusCode);
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
}