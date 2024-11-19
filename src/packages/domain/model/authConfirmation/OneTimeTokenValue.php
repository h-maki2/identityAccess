<?php

namespace packages\domain\model\authConfirmation;

class OneTimeTokenValue
{
    readonly string $value;

    private const TOKEN_LENGTH = 26;

    private function __construct(string $value)
    {
        if (strlen($value) !== self::TOKEN_LENGTH) {
            throw new \InvalidArgumentException('無効なトークンです');
        }

        $this->value = $value;
    }

    public static function create(): self
    {
        return new self(bin2hex(random_bytes(self::TOKEN_LENGTH / 2)));
    }

    public static function reconstruct(string $value): self
    {
        return new self($value);
    }
}