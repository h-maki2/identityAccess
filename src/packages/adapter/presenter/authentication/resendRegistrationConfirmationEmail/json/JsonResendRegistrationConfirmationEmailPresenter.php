<?php

namespace packages\adapter\presenter\authentication\resendRegistrationConfirmationEmail\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailResult;

class JsonResendRegistrationConfirmationEmailPresenter implements JsonPresenter
{
    private ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult;

    public function __construct(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult)
    {
        $this->resendRegistrationConfirmationEmailResult = $resendRegistrationConfirmationEmailResult;
    }

    public function jsonResponseData(): JsonResponseData
    {
        return new JsonResponseData($this->responseData($this->resendRegistrationConfirmationEmailResult), $this->httpStatus());
    }

    private function responseData(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): array
    {
        if ($this->resendRegistrationConfirmationEmailResult->validationError) {
            return [
                'validationErrorMessage' => $resendRegistrationConfirmationEmailResult->validationErrorMessage
            ];
        }
        
        return [];
    }

    private function httpStatus(): HttpStatus
    {
        return $this->resendRegistrationConfirmationEmailResult->validationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}