<?php

namespace packages\domain\service\common\hash;

class Argon2Hash
{
    private const HASH_OPTIONS = [
        'memory_cost' => 65536,
        'time_cost' => 4, // 4回のハッシュ化
        'threads' => 1 // 1スレッド
    ];

    /**
     * Argon2アルゴリズムを用いてハッシュ化する
     */
    public static function hashValue(string $value): string
    {
        return password_hash($value, PASSWORD_ARGON2ID, self::HASH_OPTIONS);
    }
}