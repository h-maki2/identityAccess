<?php

namespace packages\adapter\presenter\common\json;

enum JsonResponseStatus: string
{
    case Success = 'success';
    case Error = 'error';
    case ValidationError = 'validation_error';
    case AuthenticationError = 'authentication_error';

    public function message(): string
    {
        return match ($this) {
            self::Success => '成功',
            self::Error => 'エラー',
            self::ValidationError => 'バリデーションエラー',
            self::AuthenticationError => '認証エラー',
        };
    }
}