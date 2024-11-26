<?php

namespace packages\adapter\presenter\error\json;

use packages\adapter\presenter\error\ErrorPresenter;

class JsonErrorPresenter implements ErrorPresenter
{
    private array $errorMessage;
    private int $statusCode;

    public function setResponse(string $errorMessage, int $statusCode): void
    {
        $this->errorMessage = [$errorMessage];
        $this->statusCode = $statusCode;
    }

    public function formatForResponse(): void
    {
        formatForResponse()->json($this->errorMessage, $this->statusCode)->send();
    }
}