<?php

namespace packages\domain\model\authTokenManage;

use DateTimeImmutable;

class AccessTokenExpirationDate
{
    readonly DateTimeImmutable $value;

    private function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    public static function fromOneHour(): self
    {
        $now = new DateTimeImmutable();
        return new self($now->modify('+1 hour'));
    }
}