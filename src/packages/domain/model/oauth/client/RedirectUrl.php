<?php

namespace packages\domain\model\oauth\client;

use InvalidArgumentException;

class RedirectUrl
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format');
        }

        $this->value = $value;
    }

    public function equals(RedirectUrl $other): bool
    {
        return $this->value === $other->value;
    }
}