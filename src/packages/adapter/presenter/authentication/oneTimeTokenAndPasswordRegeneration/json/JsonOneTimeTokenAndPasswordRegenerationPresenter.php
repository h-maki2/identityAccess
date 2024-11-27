<?php

namespace packages\adapter\presenter\authentication\oneTimeTokenAndPasswordRegeneration\json;

use packages\application\authentication\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegenerationOutputBoundary;
use packages\application\authentication\oneTimeTokenAndPasswordRegeneration\OneTimeTokenAndPasswordRegenerationResult;

class JsonOneTimeTokenAndPasswordRegenerationPresenter implements OneTimeTokenAndPasswordRegenerationOutputBoundary
{
    private array $responseData;
    private int $statusCode;

    public function formatForResponse(OneTimeTokenAndPasswordRegenerationResult $oneTimeTokenAndPasswordRegenerationResult): void
    {
        $this->setResponseData($oneTimeTokenAndPasswordRegenerationResult);
        $this->setStatusCode($oneTimeTokenAndPasswordRegenerationResult);
    }

    public function response(): mixed
    {
        return response()->json($this->responseData, $this->statusCode);
    }

    private function setResponseData(OneTimeTokenAndPasswordRegenerationResult $oneTimeTokenAndPasswordRegenerationResult): void
    {
        $this->responseData = [
            'validationErrorMessage' => $oneTimeTokenAndPasswordRegenerationResult->validationErrorMessage,
            'oneTimeTokenValue' => $oneTimeTokenAndPasswordRegenerationResult->oneTimeTokenValue
        ];
    }

    private function setStatusCode(OneTimeTokenAndPasswordRegenerationResult $oneTimeTokenAndPasswordRegenerationResult): void
    {
        $this->statusCode = $oneTimeTokenAndPasswordRegenerationResult->validationError ? 400 : 200;
    }
}