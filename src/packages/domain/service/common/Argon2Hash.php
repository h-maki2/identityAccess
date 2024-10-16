<?php

namespace packages\service\common;

class Argon2Hash extends AHashService
{
    /**
     * Argon2アルゴリズムを用いてハッシュ化する
     */
    public static function hashValue(string $value): string
    {
        return password_hash($value, PASSWORD_ARGON2I, self::HASH_OPTIONS);
    }
}