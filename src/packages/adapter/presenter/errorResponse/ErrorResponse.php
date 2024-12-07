<?php

namespace packages\adapter\presenter\errorResponse;

use packages\adapter\presenter\common\json\HttpStatus;

interface ErrorResponse
{
    public function response(HttpStatus $statusCode): mixed;
}