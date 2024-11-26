<?php

namespace App\Exceptions;

use DomainException;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Laravel\Passport\Exceptions\AuthenticationException;
use packages\adapter\presenter\error\ErrorPresenter;
use packages\application\common\exception\TransactionException;
use packages\domain\model\authenticationInformation\AuthenticationInformation;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * 登録された例外ハンドリングのカスタム処理
     */
    public function register()
    {
    }

    /**
     * HTTPレスポンスをカスタマイズ
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof InvalidArgumentException) {
            return response()->json([
                'error' => 'Bad Request',
            ], 400);
        }

        if ($exception instanceof DomainException) {
            return response()->json([
                'error' => 'Bad Request',
            ], 400);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }

        if ($exception instanceof TransactionException) {
            Log::error($exception->getMessage(), ['exception' => $exception]);
            return response()->json([
                'error' => 'Internal Server Error',
            ], 500);
        }

        if ($exception instanceof Exception) {
            Log::error($exception->getMessage(), ['exception' => $exception]);
            return response()->json([
                'error' => 'Internal Server Error',
            ], 500);
        }

        // デフォルトの例外処理
        return parent::render($request, $exception);
    }
}