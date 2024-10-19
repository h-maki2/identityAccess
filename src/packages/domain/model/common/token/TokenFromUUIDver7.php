<?php

namespace packages\domain\model\common\token;

abstract class TokenFromUUIDver7
{
    private const TOKEN_LENGTH = 36;

    protected function isValidLength(string $value): bool
    {
        return strlen($value) !== self::TOKEN_LENGTH;
    }

    protected function isValidFormat(string $value): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value);
    } 
}