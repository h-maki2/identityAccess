<?php

namespace packages\adapter\presenter\errorResponse;

use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;

class JsonErrorResponse extends JsonPresenter implements ErrorResponse
{
    public function response(HttpStatus $httpStatus): mixed
    {
        return $this->jsonResponse(null, $httpStatus);
    }
}