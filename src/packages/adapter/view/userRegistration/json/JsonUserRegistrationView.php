<?php

namespace packages\adapter\view\userRegistration\json;

use Illuminate\Http\JsonResponse;
use packages\adapter\presenter\common\json\JsonResponseData;
use packages\adapter\presenter\userRegistration\UserRegistrationView;

class JsonUserRegistrationView extends UserRegistrationView
{
    public function response(): JsonResponse
    {
        $jsonResponseData = new JsonResponseData($this->responseData, $this->httpStatus);
        return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
    }
}