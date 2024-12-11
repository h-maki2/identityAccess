<?php

namespace packages\adapter\presenter\authentication\verifiedUpdate\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageOutputBoundary;
use packages\application\authentication\verifiedUpdate\display\DisplayVerifiedUpdatePageResult;

class JsonDisplayVerifiedUpdatePagePresenter
{
    private DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult;

    public function __construct(DisplayVerifiedUpdatePageResult $displayVerifiedUpdatePageResult)
    {
        $this->displayVerifiedUpdatePageResult = $displayVerifiedUpdatePageResult;   
    }

    public function jsonResponseData(): JsonPresenter
    {
        return new JsonPresenter($this->responseData(), $this->httpStatus());
    }

    private function responseData(): array
    {
        if ($this->displayVerifiedUpdatePageResult->validationError) {
            return [
                'validationErrorMessage' => $this->displayVerifiedUpdatePageResult->validationErrorMessage
            ];
        }

        return [
            'oneTimeTokenValue' => $this->displayVerifiedUpdatePageResult->oneTimeTokenValue
        ];
    }

    private function httpStatus(): HttpStatus
    {
        return $this->displayVerifiedUpdatePageResult->validationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}