<?php

namespace packages\domain\model\authConfirmation;

use DateTimeImmutable;

class TemporaryTokenExpiration
{
    private DateTimeImmutable $value;

    private function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    public static function create(): self
    {
        return new self(new DateTimeImmutable('+24 hours'));
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
     * トークンが有効期限切れかどうかを判定
     */
    public function isExpired(DateTimeImmutable $currentDateTime): bool
    {
        return $currentDateTime > $this->value;
    }
}