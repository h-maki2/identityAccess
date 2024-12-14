<?php

namespace packages\adapter\presenter\authentication\login\json;

use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\Http;
use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\authentication\login\LoginOutputBoundary;
use packages\application\authentication\login\LoginResult;

class JsonLoginPresenter implements JsonPresenter
{
    private LoginResult $loginResult;

    public function __construct(LoginResult $loginResult)
    {
        $this->loginResult = $loginResult;
    }

    public function jsonResponseData(): JsonResponseData
    {
        return new JsonResponseData($this->responseData(), $this->httpStatus());
    }

    private function responseData(): array
    {
        if ($this->loginResult->loginSucceeded) {
            return [
                'authorizationUrl' => $this->loginResult->authorizationUrl
            ];
        }

        return [
            'accountLocked' => $this->loginResult->accountLocked
        ];
    }

    private function httpStatus(): HttpStatus
    {
        return $this->loginResult->loginSucceeded ? HttpStatus::Success : HttpStatus::BadRequest;
    }
}