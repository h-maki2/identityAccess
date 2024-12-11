<?php

namespace packages\adapter\presenter\authentication\verifiedUpdate\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateOutputBoundary;
use packages\application\authentication\verifiedUpdate\update\VerifiedUpdateResult;

class JsonVerifiedUpdatePresenter implements JsonPresenter
{
    private VerifiedUpdateResult $verifiedUpdateResult;

    public function __construct(VerifiedUpdateResult $verifiedUpdateResult)
    {
        $this->verifiedUpdateResult = $verifiedUpdateResult;
    }

    public function jsonResponseData(): JsonResponseData
    {
        return new JsonResponseData($this->responseData(), $this->httpStatus());
    }

    private function responseData(): array
    {
        if ($this->verifiedUpdateResult->validationError) {
            return [
                'validationErrorMessage' => $this->verifiedUpdateResult->validationErrorMessage
            ];
        }

        return [];
    }

    private function httpStatus(): HttpStatus
    {
        return $this->verifiedUpdateResult->validationError ? HttpStatus::BadRequest : HttpStatus::Success;
    }
}