<?php

namespace packages\adapter\presenter\userProfile\fetch\json;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\application\userProfile\fetch\FetchUserProfileResult;

class JsonFetchUserProfilePresenter implements JsonPresenter
{
    private FetchUserProfileResult $result;

    public function __construct(FetchUserProfileResult $result)
    {
        $this->result = $result;
    }

    public function jsonResponseData(): JsonResponseData
    {
        return new JsonResponseData($this->responseData(), $this->httpStatus());
    }

    public function responseData(): array
    {
        if (!$this->result->isExistsUserProfile) {
            return [];
        }

        return [
            'userProfileId' => $this->result->userProfileId,
            'userName' => $this->result->userName,
            'selfIntroductionText' => $this->result->selfIntroductionText
        ];
    }

    private function httpStatus(): HttpStatus
    {
        return $this->result->isExistsUserProfile ? HttpStatus::Success : HttpStatus::NoContent;
    }
}