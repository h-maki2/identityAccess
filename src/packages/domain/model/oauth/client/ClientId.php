<?php

namespace packages\domain\model\oauth\client;

class ClientId
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Invalid client id');
        }

        if (!ctype_digit($value)) {
            throw new \InvalidArgumentException('Invalid client id');
        }

        $this->value = $value;
    }
}