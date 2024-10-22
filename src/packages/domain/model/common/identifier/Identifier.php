<?php

namespace packages\domain\model\common\identifier;

interface Identifier
{
    /**
     * 適切な文字列の長さかどうかを判定
     */
    public function isValidLength(string $value): bool;

    /**
     * 適切な形式かどうかを判定
     */
    public function isValidFormat(string $value): bool;
}