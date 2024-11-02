<?php

namespace packages\domain\model\userProfile;

use DomainException;
use InvalidArgumentException;

class FailedLoginAttempts
{
    readonly int $value;

    private const MAX_VALUE = 10;
    private const MIN_VALUE = 0;

    private function __construct(int $value)
    {
        if ($value < self::MIN_VALUE || $value > self::MAX_VALUE) {
            throw new InvalidArgumentException('無効な値です。');
        }
        $this->value = $value;
    }

    public static function initialization(): self
    {
        return new self(0);
    }

    public static function reconstruct(int $value): self
    {
        return new self($value);
    }

    public function add(): self
    {
        $addedValue = $this->value++;
        if ($addedValue > self::MAX_VALUE) {
            throw new DomainException('ログイン試行回数が最大値を超えました。');
        }
        return new self($addedValue);
    }
}