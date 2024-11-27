<?php

namespace packages\adapter\presenter\authentication\verifiedUpdate\json;

use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageOutputBoundary;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageResult;

class JsonDisplayVerifiedUpdatePagePresenter implements DisplayVerifiedUpdatePageOutputBoundary
{
    private array $responseData;
    private int $statusCode;

    public function formatForResponse(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult): void
    {
        $this->setResponseData($displayVerifiedUpdatePageResult);
        $this->setStatusCode($displayVerifiedUpdatePageResult);
    }

    public function response(): mixed
    {
        return response()->json($this->responseData, $this->statusCode);
    }

    private function setResponseData(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult): void
    {
        $this->responseData = [
            'validationErrorMessage' => $displayVerifiedUpdatePageResult->validationErrorMessage,
            'oneTimeTokenValue' => $displayVerifiedUpdatePageResult->oneTimeTokenValue,
            'oneTimePassword' => $displayVerifiedUpdatePageResult->oneTimePassword
        ];
    }

    private function setStatusCode(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult): void
    {
        $this->statusCode = $displayVerifiedUpdatePageResult->validationError ? 400 : 200;
    }
}