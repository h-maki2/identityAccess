<?php

namespace App\Exceptions;

use DomainException;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use InvalidArgumentException;
use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\errorResponse\ErrorResponse;
use packages\adapter\presenter\errorResponse\JsonErrorResponse;
use packages\application\common\exception\TransactionException;
use packages\domain\model\common\exception\AuthenticationException;
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
            $jsonResponseData = JsonErrorResponse::get(HttpStatus::BadRequest);
            return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
        }

        if ($exception instanceof DomainException) {
            $jsonResponseData = JsonErrorResponse::get(HttpStatus::BadRequest);
            return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
        }

        if ($exception instanceof AuthenticationException) {
            $jsonResponseData = JsonErrorResponse::get(HttpStatus::Unauthorized);
            return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
        }

        if ($exception instanceof TransactionException) {
            $jsonResponseData = JsonErrorResponse::get(HttpStatus::InternalServerError);
            return response()->json($jsonResponseData->value, $jsonResponseData->httpStatusCode());
        }

        // デフォルトの例外処理
        return parent::render($request, $exception);
    }

}
