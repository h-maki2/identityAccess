<?php

namespace packages\adapter\presenter\userRegistration;

use packages\adapter\presenter\common\json\HttpStatus;

abstract class UserRegistrationView
{
    protected HttpStatus $httpStatus;
    protected array $responseData;

    abstract public function response(): mixed;

    public function setHttpStatus(HttpStatus $httpStatus): void
    {
        $this->httpStatus = $httpStatus;
    }

    public function setResponseData(array $responseData): void
    {
        $this->responseData = $responseData;
    }
}