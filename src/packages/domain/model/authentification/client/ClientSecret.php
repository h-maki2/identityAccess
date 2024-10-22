<?php

namespace packages\domain\model\authentification\client;

use InvalidArgumentException;

class ClientSecret
{
    readonly string $hashedValue;

    public function __construct(string $hashedValue)
    {
        if (empty($hashedValue)) {
            throw new InvalidArgumentException('クライアントシークレットが空です。');
        }
        $this->hashedValue = $hashedValue;        
    }

    public function equals(string $value): bool
    {
        return password_verify($value, $this->hashedValue);
    }
}