<?php

namespace packages\domain\model\definitiveRegistrationConfirmation;

use DateTimeImmutable;

class OneTimeTokenExpiration
{
    private DateTimeImmutable $value;

    private const EXPIRATION_HOURS = 24;

    private function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    public static function create(): self
    {
        return new self(new DateTimeImmutable('+' . self::EXPIRATION_HOURS . ' hours'));
    }

    public static function reconstruct(DateTimeImmutable $value): self
    {
        return new self($value);
    }

    public static function expirationHours(): int
    {
        return self::EXPIRATION_HOURS;
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