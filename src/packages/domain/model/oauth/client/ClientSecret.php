<?php

namespace packages\domain\model\oauth\client;

class ClientSecret
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Invalid client secret');
        }

        $this->value = $value;
    }

    public function equals(ClientSecret $other): bool
    {
        return $this->value === $other->value;
    }
}