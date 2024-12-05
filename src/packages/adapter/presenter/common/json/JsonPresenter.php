<?php

namespace packages\adapter\presenter\common\json;

abstract class JsonPresenter
{
    protected function jsonResponse(?array $responseData, JsonResponseStatus $jsonResponseStatus, int $httpStatusCode): mixed
    {
        return response()->json([
            'status' => $jsonResponseStatus->value,
            'message' => $jsonResponseStatus->message(),
            'data' => $responseData,
        ], $httpStatusCode);
    }
}