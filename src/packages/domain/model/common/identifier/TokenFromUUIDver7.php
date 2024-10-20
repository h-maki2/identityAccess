<?php

namespace packages\domain\model\common\identifier;

abstract class IdentifierFromUUIDver7
{
    private const TOKEN_LENGTH = 36;

    /**
     * 適切な文字列の長さかどうかを判定
     */
    protected function isValidLength(string $value): bool
    {
        return strlen($value) !== self::TOKEN_LENGTH;
    }

    /**
     * 適切な形式かどうかを判定
     */
    protected function isValidFormat(string $value): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value);
    } 
}