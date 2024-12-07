<?php

namespace App\Exceptions;

use DomainException;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use InvalidArgumentException;
use packages\adapter\presenter\common\json\HttpStatus;
use packages\adapter\presenter\errorResponse\ErrorResponse;
use packages\application\common\exception\TransactionException;
use Throwable;

class Handler extends ExceptionHandler
{
    private ErrorResponse $errorResponse;

    public function __construct(ErrorResponse $errorResponse)
    {
        $this->errorResponse = $errorResponse;
        parent::__construct(app());
    }

    public function register()
    {
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof InvalidArgumentException) {
            return $this->errorResponse->response(HttpStatus::BadRequest);
        }

        if ($exception instanceof DomainException) {
            return $this->errorResponse->response(HttpStatus::BadRequest);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->errorResponse->response(HttpStatus::Unauthorized);
        }

        if ($exception instanceof TransactionException) {
            return $this->errorResponse->response(HttpStatus::InternalServerError);
        }

        // デフォルトの例外処理
        return parent::render($request, $exception);
    }

}
