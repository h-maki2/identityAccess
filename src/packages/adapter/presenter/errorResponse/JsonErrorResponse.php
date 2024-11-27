<?php

namespace packages\adapter\presenter\errorResponse;

class JsonErrorResponse implements ErrorResponse
{
    public function response(string $errorMessage, int $statusCode): mixed
    {
        return response()->json([
            'error' => $errorMessage,
        ], $statusCode);
    }
}