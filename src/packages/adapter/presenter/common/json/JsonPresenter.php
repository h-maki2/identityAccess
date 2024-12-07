<?php

namespace packages\adapter\presenter\common\json;

abstract class JsonPresenter
{
    protected function jsonResponse(?array $responseData, HttpStatus $httpStatus): mixed
    {
        if ($httpStatus->isSuccess()) {
            return response()->json([
                'success' => true,
                'data' => $responseData,
            ], $httpStatus->value);
        }

        return response()->json([
            'success' => false,
            'error' => [
                'code' => $httpStatus->code(),
                'details' => $responseData,
            ]
        ], $httpStatus->value);
    }
}