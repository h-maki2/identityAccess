<?php

use packages\adapter\presenter\common\json\HttpStatus;

abstract class UserRegistrationView
{
    protected HttpStatus $httpStatus;
    protected mixed $responseData;

    abstract public function response(): mixed;

    public function setHttpStatus(HttpStatus $httpStatus): void
    {
        $this->httpStatus = $httpStatus;
    }

    public function setResponseData(mixed $responseData): void
    {
        $this->responseData = $responseData;
    }
}