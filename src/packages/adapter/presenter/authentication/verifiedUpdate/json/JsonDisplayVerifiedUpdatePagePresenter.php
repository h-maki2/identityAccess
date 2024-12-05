<?php

namespace packages\adapter\presenter\authentication\verifiedUpdate\json;

use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageOutputBoundary;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageResult;

class JsonDisplayVerifiedUpdatePagePresenter extends JsonPresenter implements DisplayVerifiedUpdatePageOutputBoundary
{
    private array $responseData;
    private int $statusCode;
    private JsonResponseStatus $jsonResponseStatus;

    public function formatForResponse(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult): void
    {
        $this->setResponseData($displayVerifiedUpdatePageResult);
        $this->setStatusCode($displayVerifiedUpdatePageResult);
        $this->setJsonResponseStatus($displayVerifiedUpdatePageResult);
    }

    public function response(): mixed
    {
        return $this->jsonResponse($this->responseData, $this->jsonResponseStatus, $this->statusCode);
    }

    private function setResponseData(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult): void
    {
        $this->responseData = [
            'validationErrorMessage' => $displayVerifiedUpdatePageResult->validationErrorMessage,
            'oneTimeTokenValue' => $displayVerifiedUpdatePageResult->oneTimeTokenValue
        ];
    }

    private function setStatusCode(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult): void
    {
        $this->statusCode = $displayVerifiedUpdatePageResult->validationError ? 400 : 200;
    }

    private function setJsonResponseStatus(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult): void
    {
        $this->jsonResponseStatus = $displayVerifiedUpdatePageResult->validationError ? JsonResponseStatus::ValidationError : JsonResponseStatus::Success;
    }
}