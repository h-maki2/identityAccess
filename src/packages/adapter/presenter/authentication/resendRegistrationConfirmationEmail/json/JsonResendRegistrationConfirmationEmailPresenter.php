<?php

namespace packages\adapter\presenter\authentication\resendRegistrationConfirmationEmail\json;

use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailOutputBoundary;
use packages\application\authentication\resendRegistrationConfirmationEmail\ResendRegistrationConfirmationEmailResult;

class JsonResendRegistrationConfirmationEmailPresenter extends JsonPresenter implements ResendRegistrationConfirmationEmailOutputBoundary
{
    private array $responseData;
    private int $statusCode;
    private JsonResponseStatus $jsonResponseStatus;

    public function formatForResponse(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): void
    {
        $this->setResponseData($resendRegistrationConfirmationEmailResult);
        $this->setStatusCode($resendRegistrationConfirmationEmailResult);
        $this->setJsonResponseStatus($resendRegistrationConfirmationEmailResult);
    }

    public function response(): mixed
    {
        return $this->jsonResponse($this->responseData, $this->jsonResponseStatus, $this->statusCode);
    }

    private function setResponseData(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): void
    {
        $this->responseData = [
            'validationErrorMessage' => $resendRegistrationConfirmationEmailResult->validationErrorMessage
        ];
    }

    private function setStatusCode(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): void
    {
        $this->statusCode = $resendRegistrationConfirmationEmailResult->validationError ? 400 : 200;
    }

    private function setJsonResponseStatus(ResendRegistrationConfirmationEmailResult $resendRegistrationConfirmationEmailResult): void
    {
        $this->jsonResponseStatus = $resendRegistrationConfirmationEmailResult->validationError ? JsonResponseStatus::ValidationError : JsonResponseStatus::Success;
    }
}