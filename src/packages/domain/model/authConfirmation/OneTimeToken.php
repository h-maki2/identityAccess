<?php

namespace packages\domain\model\authConfirmation;

use DateTimeImmutable;
use InvalidArgumentException;

class OneTimeToken
{
    readonly string $value;
    private OneTimeTokenExpiration $tokenExpiration;

    private const TOKEN_LENGTH = 26;

    private function __construct(
        string $value,
        OneTimeTokenExpiration $tokenExpiration
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
            OneTimeTokenExpiration::create()
        );
    }

    public static function reconstruct(
        string $value,
        OneTimeTokenExpiration $tokenExpiration
    ): self
    {
        return new self($value, $tokenExpiration);
    }

    public function expiration(): OneTimeTokenExpiration
    {
        return $this->tokenExpiration;
    }

    public function isExpired(DateTimeImmutable $currentDateTime): bool
    {
        return $this->tokenExpiration->isExpired($currentDateTime);
    }
}