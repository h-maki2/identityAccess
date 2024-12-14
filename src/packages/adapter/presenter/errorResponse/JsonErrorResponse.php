<?php

namespace packages\adapter\presenter\errorResponse;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonResponseData;

class JsonErrorResponse
{
    public static function get(HttpStatus $httpStatus): JsonResponseData
    {
        return new JsonResponseData([], $httpStatus);
    }
}