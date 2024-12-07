<?php

namespace packages\adapter\presenter\authentication\resendRegistrationConfirmationEmail\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailOutputBoundary;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailResult;

class JsonResendRegistrationConfirmationEmailPresenter extends JsonPresenter implements ResendRegistrationConfirmationEmailOutputBoundary
{
    private array $responseData;
    private HttpStatus $httpStatus;

    public function formatForResponse(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): void
    {
        $this->setResponseData($resendRegistrationConfirmationEmailResult);
        $this->setHttpStatus($resendRegistrationConfirmationEmailResult);
    }

    public function response(): mixed
    {
        return $this->jsonResponse($this->responseData, $this->httpStatus);
    }

    private function setResponseData(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): void
    {
        if ($resendRegistrationConfirmationEmailResult->validationError) {
            $this->responseData = [
                'validationErrorMessage' => $resendRegistrationConfirmationEmailResult->validationErrorMessage
            ];
            return;
        }
        
        $this->responseData = [];
    }

    private function setHttpStatus(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): void
    {
        $this->httpStatus = $resendRegistrationConfirmationEmailResult->validationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}