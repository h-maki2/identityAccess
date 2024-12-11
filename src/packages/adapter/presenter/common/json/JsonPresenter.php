<?php

namespace packages\adapter\presenter\common\json;

class JsonPresenter
{
    readonly array $responseData;
    private HttpStatus $httpStatus;

    public function __construct(array $responseData, HttpStatus $httpStatus)
    {
        if ($httpStatus->isSuccess()) {
            $this->setSuccessResponse($responseData, $httpStatus);
        } else {
            $this->setErrorResponse($responseData, $httpStatus);
        }
    }
    
    public function httpStatusCode(): int
    {
        return (int) $this->httpStatus->value;
    }

    private function setSuccessResponse(array $responseData, HttpStatus $httpStatus)
    {
        $this->responseData = [
            'success' => true,
            'data' => $responseData,
        ];
    }

    private function setErrorResponse(array $responseData, HttpStatus $httpStatus)
    {
        $this->responseData = [
            'success' => false,
            'error' => [
                'code' => $httpStatus->code(),
                'details' => $responseData,
            ]
        ];
    }
}