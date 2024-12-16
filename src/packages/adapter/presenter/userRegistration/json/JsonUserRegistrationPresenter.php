<?php

namespace packages\adapter\presenter\userRegistration\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\adapter\presenter\userRegistration\UserRegistrationPresenter;
use packages\application\userRegistration\UserRegistrationOutputBoundary;
use packages\application\userRegistration\UserRegistrationResult;

class JsonUserRegistrationPresenter extends UserRegistrationPresenter
{
    public function __construct(UserRegistrationResult $result)
    {
        parent::__construct($result);
    }

    public function viewResponse(): JsonResponseData
    {
        return new JsonResponseData($this->responseData($this->result), $this->httpStatus());
    }

    private function httpStatus(): HttpStatus
    {
        return $this->result->validationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}