<?php

namespace packages\adapter\presenter\errorResponse;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;

class JsonErrorResponse
{
    public static function get(HttpStatus $httpStatus): JsonPresenter
    {
        return new JsonPresenter([], $httpStatus);
    }
}