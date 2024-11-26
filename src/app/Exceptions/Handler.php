<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use InvalidArgumentException;
use packages\adapter\presenter\error\ErrorPresenter;
use Throwable;

class Handler extends ExceptionHandler
{
    private ErrorPresenter $errorPresenter;

    public function __construct(ErrorPresenter $errorPresenter)
    {
        $this->errorPresenter = $errorPresenter; 
    }

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
        // 特定の例外をカスタマイズ
        if ($exception instanceof InvalidArgumentException) {
            $this->errorPresenter->setResponse($exception->getMessage(), 400);
            $this->errorPresenter->response();
            return;
        }

        // デフォルトの例外処理
        return parent::render($request, $exception);
    }
}