<?php

namespace packages\service\common;

abstract class AHashService
{
    protected const HASH_OPTIONS = [
        'memory_cost' => 1 << 17, // 128MB
        'time_cost' => 4, // 4回のハッシュ化
        'threads' => 1 // 1スレッド
    ];

    /**
     * ハッシュ化する
     */
    abstract public static function hashValue(string $value): string;
}