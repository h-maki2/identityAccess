<?php

namespace packages\adapter\presenter\common\json;

enum JsonResponseStatus: string
{
    case Sucess = 'success';
    case Error = 'error';
    case ValidationError = 'validation_error';

    public function message(): string
    {
        return match ($this) {
            self::Sucess => '成功',
            self::Error => 'エラー',
            self::ValidationError => 'バリデーションエラー',
        };
    }
}