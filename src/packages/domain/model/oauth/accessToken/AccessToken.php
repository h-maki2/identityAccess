<?php

namespace packages\domain\model\oauth\accessToken;

use InvalidArgumentException;

abstract class AccessToken
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('アクセストークンが空です。');
        }
        $this->value = $value;
    }

    abstract public function id(): string;
}