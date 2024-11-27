<?php

namespace packages\adapter\presenter\errorResponse;

interface ErrorResponse
{
    public function response(string $errorMesasge, int $statusCode): mixed;
}