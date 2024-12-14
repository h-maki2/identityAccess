<?php

namespace packages\adapter\presenter\common\json;

class JsonResponseData
{
    readonly array $value;
    private HttpStatus $httpStatus;

    public function __construct(array $responseData, HttpStatus $httpStatus)
    {
        if ($httpStatus->isSuccess()) {
            $this->setSuccessResponse($responseData);
        } else {
            $this->setErrorResponse($responseData, $httpStatus);
        }

        $this->httpStatus = $httpStatus;
    }
    
    public function httpStatusCode(): int
    {
        return (int) $this->httpStatus->value;
    }

    private function setSuccessResponse(array $responseData)
    {
        $this->value = [
            'success' => true,
            'data' => $responseData,
        ];
    }

    private function setErrorResponse(array $responseData, HttpStatus $httpStatus)
    {
        $this->value = [
            'success' => false,
            'error' => [
                'code' => $httpStatus->code(),
                'details' => $responseData,
            ]
        ];
    }
}