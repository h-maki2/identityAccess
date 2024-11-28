<?php

namespace packages\domain\model\authConfirmation;

use InvalidArgumentException;

class OneTimePassword
{
    readonly string $value;

    private const LENGTH = 6;

    private function __construct(string $value)
    {
        if (strlen($value) !== self::LENGTH) {
            throw new InvalidArgumentException('無効なワンタイムパスワードです。');
        }

        $this->value = $value;
    }

    public static function create(): self
    {
        return new self((string)random_int(100000, 999999));
    }

    public static function reconstruct(int $value): self
    {
        return new self($value);
    }

    public function equals(OneTimePassword $other): bool
    {
        return $this->value === $other->value;
    }
}