<?php

namespace packages\adapter\presenter\authentication\verifiedUpdate\json;

use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateOutputBoundary;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateResult;

class JsonVerifiedUpdatePresenter implements VerifiedUpdateOutputBoundary
{
    private array $responseData;
    private int $statusCode;

    public function formatForResponse(VerifiedUpdateResult $verifiedUpdateResult): void
    {
        $this->setResponseData($verifiedUpdateResult);
        $this->setStatusCode($verifiedUpdateResult);
    }

    public function response(): mixed
    {
        return response()->json($this->responseData, $this->statusCode);
    }

    private function setResponseData(VerifiedUpdateResult $verifiedUpdateResult): void
    {
        $this->responseData = [
            'validationErrorMessage' => $verifiedUpdateResult->validationErrorMessage
        ];
    }

    private function setStatusCode(VerifiedUpdateResult $verifiedUpdateResult): void
    {
        $this->statusCode = $verifiedUpdateResult->validationError ? 400 : 200;
    }
}