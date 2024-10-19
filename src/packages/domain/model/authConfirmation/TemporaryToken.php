<?php

namespace packages\domain\model\authConfirmation;

use InvalidArgumentException;

class TemporaryToken
{
    readonly string $value;

    private const TOKEN_LENGTH = 36;

    public function __construct(string $value)
    {
        if ($value !== self::TOKEN_LENGTH) {
            throw new InvalidArgumentException('TemporaryTokenは36文字です。');
        }

        if (!$this->isUuidV7($value)) {
            throw new InvalidArgumentException('UUID ver7の形式になっていません。');
        }

        $this->value = $value;
    }

    private function isUuidV7(string $value): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value);
    }
}