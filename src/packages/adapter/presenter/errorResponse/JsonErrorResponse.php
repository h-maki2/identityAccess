<?php

namespace packages\adapter\presenter\errorResponse;

use packages\adapter\presenter\common\json\JsonPresenter;
use packages\adapter\presenter\common\json\JsonResponseStatus;

class JsonErrorResponse extends JsonPresenter implements ErrorResponse
{
    public function response(string $errorMessage, int $statusCode): mixed
    {
        return $this->jsonResponse(null, JsonResponseStatus::Error, $statusCode);
    }
}