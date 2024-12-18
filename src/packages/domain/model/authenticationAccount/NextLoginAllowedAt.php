<?php

namespace packages\domain\model\authenticationAccount;

use DateInterval;
use DateTimeImmutable;

/**
 * 再ログイン可能な日時
 */
class NextLoginAllowedAt
{
    private DateTimeImmutable $value;

    private function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    public static function create(): self
    {
        $now = new DateTimeImmutable();
        return new self($now->add(new DateInterval('PT10M')));
    }

    public static function reconstruct(DateTimeImmutable $value): self
    {
        return new self($value);
    }

    public function formattedValue(): string
    {   
        return $this->value->format('Y-m-d H:i');
    }

    /**
     * 再ログインが可能かどうかを判定
     */
    public function isAvailable(DateTimeImmutable $currentDateTime): bool
    {
        return $currentDateTime >= $this->value;
    }
}