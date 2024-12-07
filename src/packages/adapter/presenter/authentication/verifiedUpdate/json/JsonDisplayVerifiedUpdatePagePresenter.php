<?php

namespace packages\adapter\presenter\authentication\verifiedUpdate\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageOutputBoundary;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageResult;

class JsonDisplayVerifiedUpdatePagePresenter extends JsonPresenter implements DisplayVerifiedUpdatePageOutputBoundary
{
    private array $responseData;
    private HttpStatus $httpStatus;

    public function formatForResponse(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult): void
    {
        $this->setResponseData($displayVerifiedUpdatePageResult);
        $this->setHttpStatus($displayVerifiedUpdatePageResult);
    }

    public function response(): mixed
    {
        return $this->jsonResponse($this->responseData, $this->httpStatus);
    }

    private function setResponseData(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult): void
    {
        if ($displayVerifiedUpdatePageResult->validationError) {
            $this->responseData = [
                'validationErrorMessage' => $displayVerifiedUpdatePageResult->validationErrorMessage
            ];
            return;
        }

        $this->responseData = [
            'oneTimeTokenValue' => $displayVerifiedUpdatePageResult->oneTimeTokenValue
        ];
    }

    private function setHttpStatus(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult): void
    {
        $this->httpStatus = $displayVerifiedUpdatePageResult->validationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}