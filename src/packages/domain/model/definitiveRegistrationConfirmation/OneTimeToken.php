<?php

namespace packages\domain\model\definitiveRegistrationConfirmation;

use DateTimeImmutable;

class OneTimeToken
{
    private OneTimeTokenValue $tokenValue;
    private OneTimeTokenExpiration $tokenExpiration;

    private function __construct(
        OneTimeTokenValue $value,
        OneTimeTokenExpiration $tokenExpiration
    )
    {
        $this->tokenValue = $value;
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

    public function tokenValue(): OneTimeTokenValue
    {
        return $this->tokenValue;
    }

    public function expirationDate(): string
    {
        return $this->tokenExpiration->formattedValue();
    }

    public function isExpired(DateTimeImmutable $currentDateTime): bool
    {
        return $this->tokenExpiration->isExpired($currentDateTime);
    }
}