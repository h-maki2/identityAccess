<?php

namespace packages\adapter\view\userRegistration\json;

use Illuminate\Http\JsonResponse;
use packages\adapter\presenter\common\json\JsonResponseData;

class JsonUserRegistrationView
{
    private JsonResponseData $responseData;

    public function __construct(JsonResponseData $responseData)
    {
        $this->responseData = $responseData;
    }

    public function response(): JsonResponse
    {
        return response()->json($this->responseData->value, $this->responseData->httpStatusCode());
    }
}