<?php

namespace packages\domain\model\authConfirmation;

use DateTimeImmutable;

class OneTimeToken
{
    readonly OneTimeTokenValue $value;
    private OneTimeTokenExpiration $tokenExpiration;

    private function __construct(
        OneTimeTokenValue $value,
        OneTimeTokenExpiration $tokenExpiration
    )
    {
        $this->value = $value;
        $this->tokenExpiration = $tokenExpiration;
    }

    public static function create(): self
    {
        return new self(
            OneTimeTokenValue::create(),
            OneTimeTokenExpiration::create()
        );
    }

    public static function reconstruct(
        OneTimeTokenValue $value,
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