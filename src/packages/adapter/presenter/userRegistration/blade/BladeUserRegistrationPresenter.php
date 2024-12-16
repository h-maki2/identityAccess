<?php

namespace packages\adapter\presenter\userRegistration\blade;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\userRegistration\UserRegistrationPresenter;
use packages\application\userRegistration\UserRegistrationResult;

class BladeUserRegistrationPresenter
{
    readonly mixed $validationError;
    readonly HttpStatus $httpStatus;

    public function __construct(UserRegistrationResult $result)
    {
        $this->setResponseData($result);
        $this->setHttpStatus($result);
    }

    public function isSuccess(): bool
    {
        return $this->httpStatus->isSuccess();
    }

    protected function setResponseData(UserRegistrationResult $result): void
    {
        if (!$result->isValidationError) {
           $this->validationError = '';
           return;
        }


        $this->validationError = $result->validationErrors;
    }

    protected function setHttpStatus(UserRegistrationResult $result): void
    {
        $this->httpStatus = $result->isValidationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}