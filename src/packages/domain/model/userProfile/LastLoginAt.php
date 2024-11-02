<?php

namespace packages\domain\model\userProfile;

use DateInterval;
use DateTimeImmutable;

class LastLoginAt
{
    readonly DateTimeImmutable $value;

    private function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    public static function initialization(): self
    {
        $now = new DateTimeImmutable();
        return new self($now->add(new DateInterval('PT10M')));
    }

    public static function reconstruct(DateTimeImmutable $value): self
    {
        return new self($value);
    }
}