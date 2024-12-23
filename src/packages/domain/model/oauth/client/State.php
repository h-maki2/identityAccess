<?php

namespace packages\domain\model\oauth\client;

use InvalidArgumentException;

class State
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('stateが空です。');
        }
        $this->value = $value;
    }
}