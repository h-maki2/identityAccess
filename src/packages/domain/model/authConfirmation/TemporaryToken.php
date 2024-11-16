<?php

namespace packages\domain\model\authConfirmation;

use DateTimeImmutable;
use InvalidArgumentException;

class TemporaryToken
{
    readonly string $value;
    private TemporaryTokenExpiration $tokenExpiration;

    private const TOKEN_LENGTH = 26;

    private function __construct(
        string $value,
        TemporaryTokenExpiration $tokenExpiration
    )
    {
        if (strlen($value) !== self::TOKEN_LENGTH) {
            throw new InvalidArgumentException('無効なトークンです');
        }

        $this->value = $value;
        $this->tokenExpiration = $tokenExpiration;
    }

    public static function create(): self
    {
        return new self(
            bin2hex(random_bytes(self::TOKEN_LENGTH / 2)),
            TemporaryTokenExpiration::create()
        );
    }

    public static function reconstruct(
        string $value,
        TemporaryTokenExpiration $tokenExpiration
    ): self
    {
        return new self($value, $tokenExpiration);
    }

    public function expiration(): TemporaryTokenExpiration
    {
        return $this->tokenExpiration;
    }

    public function isExpired(DateTimeImmutable $currentDateTime): bool
    {
        return $this->tokenExpiration->isExpired($currentDateTime);
    }
}