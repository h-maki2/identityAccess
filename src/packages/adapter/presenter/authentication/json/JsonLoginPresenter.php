<?php

namespace packages\adapter\presenter\json\authentication;

use packages\application\authentication\login\LoginOutputBoundary;
use packages\application\authentication\login\LoginResult;

class JsonLoginPresenter extends LoginOutputBoundary
{
    private array $responseData;
    private int $statusCode;

    protected function __construct(LoginResult $loginResult)
    {
        $this->setResponseData($loginResult);
        $this->setStatusCode($loginResult);
    }

    public static function create(LoginResult $loginResult): self
    {
        return new self($loginResult);
    }

    public function response(): void
    {
        response()->json($this->responseData, $this->statusCode)->send();
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