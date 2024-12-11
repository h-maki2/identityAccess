<?php

namespace App\Exceptions;

use DomainException;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use InvalidArgumentException;
use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\errorResponse\ErrorResponse;
use packages\adapter\presenter\errorResponse\JsonErrorResponse;
use packages\application\common\exception\TransactionException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function __construct()
    {
        parent::__construct(app());
    }

    public function register()
    {
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof InvalidArgumentException) {
            $jsonResponse = JsonErrorResponse::get(HttpStatus::BadRequest);
            return response()->json($jsonResponse->responseData, $jsonResponse->httpStatusCode());
        }

        if ($exception instanceof DomainException) {
            $jsonResponse = JsonErrorResponse::get(HttpStatus::BadRequest);
            return response()->json($jsonResponse->responseData, $jsonResponse->httpStatusCode());
        }

        if ($exception instanceof AuthenticationException) {
            $jsonResponse = JsonErrorResponse::get(HttpStatus::Unauthorized);
            return response()->json($jsonResponse->responseData, $jsonResponse->httpStatusCode());
        }

        if ($exception instanceof TransactionException) {
            $jsonResponse = JsonErrorResponse::get(HttpStatus::InternalServerError);
            return response()->json($jsonResponse->responseData, $jsonResponse->httpStatusCode());
        }

        // デフォルトの例外処理
        return parent::render($request, $exception);
    }

}
