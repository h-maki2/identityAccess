<?php

namespace packages\adapter\presenter\authentication\ResendRegistrationConfirmationEmail\json;

use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailOutputBoundary;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailResult;

class JsonResendRegistrationConfirmationEmailPresenter implements ResendRegistrationConfirmationEmailOutputBoundary
{
    private array $responseData;
    private int $statusCode;

    public function formatForResponse(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): void
    {
        $this->setResponseData($resendRegistrationConfirmationEmailResult);
        $this->setStatusCode($resendRegistrationConfirmationEmailResult);
    }

    public function response(): mixed
    {
        return response()->json($this->responseData, $this->statusCode);
    }

    private function setResponseData(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): void
    {
        $this->responseData = [
            'validationErrorMessage' => $resendRegistrationConfirmationEmailResult->validationErrorMessage,
            'oneTimeTokenValue' => $resendRegistrationConfirmationEmailResult->oneTimeTokenValue
        ];
    }

    private function setStatusCode(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): void
    {
        $this->statusCode = $resendRegistrationConfirmationEmailResult->validationError ? 400 : 200;
    }
}