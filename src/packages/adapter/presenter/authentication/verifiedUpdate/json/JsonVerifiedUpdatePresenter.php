<?php

namespace packages\adapter\presenter\authentication\verifiedUpdate\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateOutputBoundary;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateResult;

class JsonVerifiedUpdatePresenter implements VerifiedUpdateOutputBoundary
{
    private array $responseData;
    private HttpStatus $httpStatus;

    public function formatForResponse(VerifiedUpdateResult $verifiedUpdateResult): void
    {
        $this->setResponseData($verifiedUpdateResult);
        $this->setHttpStatus($verifiedUpdateResult);
    }

    public function response(): mixed
    {
        return response()->json($this->responseData, $this->httpStatus);
    }

    private function setResponseData(VerifiedUpdateResult $verifiedUpdateResult): void
    {
        if ($verifiedUpdateResult->validationError) {
            $this->responseData = [
                'validationErrorMessage' => $verifiedUpdateResult->validationErrorMessage
            ];
            return;
        }

        $this->responseData = [];
    }

    private function setHttpStatus(VerifiedUpdateResult $verifiedUpdateResult): void
    {
        $this->httpStatus = $verifiedUpdateResult->validationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}