<?php

namespace packages\adapter\presenter\changePassword\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\application\changePassword\ChangePasswordResult;

class JsonChangePasswordPresenter
{
    private ChangePasswordResult $result;

    public function __construct(ChangePasswordResult $result)
    {
        $this->result = $result;
    }

    public function jsonResponseData(): JsonResponseData
    {
        return new JsonResponseData($this->responseData(), $this->httpStatusCode());
    }

    private function responseData(): array
    {
        if (!$this->result->isValidationError) {
            return [];
        }

        return $this->result->validationErrorMessageList;
    }

    private function httpStatusCode(): HttpStatus
    {
        return $this->result->isValidationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}