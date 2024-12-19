<?php

namespace packages\adapter\presenter\authentication\resendRegistrationConfirmationEmail\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\application\authentication\resendDefinitiveRegistrationConfirmation\ResendDefinitiveRegistrationConfirmationResult;

class JsonResendRegistrationConfirmationEmailPresenter implements JsonPresenter
{
    private ResendDefinitiveRegistrationConfirmationResult $resendDefinitiveRegistrationConfirmationResult;

    public function __construct(ResendDefinitiveRegistrationConfirmationResult $resendDefinitiveRegistrationConfirmationResult)
    {
        $this->resendDefinitiveRegistrationConfirmationResult = $resendDefinitiveRegistrationConfirmationResult;
    }

    public function jsonResponseData(): JsonResponseData
    {
        return new JsonResponseData($this->responseData($this->resendDefinitiveRegistrationConfirmationResult), $this->httpStatus());
    }

    private function responseData(ResendDefinitiveRegistrationConfirmationResult $resendDefinitiveRegistrationConfirmationResult): array
    {
        if ($this->resendDefinitiveRegistrationConfirmationResult->validationError) {
            return [
                'validationErrorMessage' => $resendDefinitiveRegistrationConfirmationResult->validationErrorMessage
            ];
        }
        
        return [];
    }

    private function httpStatus(): HttpStatus
    {
        return $this->resendDefinitiveRegistrationConfirmationResult->validationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}