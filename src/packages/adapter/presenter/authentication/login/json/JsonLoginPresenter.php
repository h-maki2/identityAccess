<?php

namespace packages\adapter\presenter\authentication\login\json;

use Illuminate\Support\Facades\Http;
use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\authentication\login\LoginOutputBoundary;
use packages\application\authentication\login\LoginResult;

class JsonLoginPresenter extends JsonPresenter implements LoginOutputBoundary
{
    private array $responseData;
    private HttpStatus $httpStatus;

    public function formatForResponse(LoginResult $loginResult): void
    {
        $this->setResponseData($loginResult);
        $this->setHttpStatus($loginResult);
    }

    public function response(): mixed
    {
        return $this->jsonResponse($this->responseData, $this->httpStatus);
    }

    private function setResponseData(LoginResult $loginResult): void
    {
        if ($loginResult->loginSucceeded) {
            $this->responseData = [
                'authorizationUrl' => $loginResult->authorizationUrl
            ];
            return;
        }


        $this->responseData = [
            'accountLocked' => $loginResult->accountLocked
        ];
    }

    private function setHttpStatus(LoginResult $loginResult): void
    {
        $this->httpStatus = $loginResult->loginSucceeded ? HttpStatus::Success : HttpStatus::BadRequest;
    }
}