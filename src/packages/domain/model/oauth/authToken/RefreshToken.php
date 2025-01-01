<?php

namespace packages\domain\model\oauth\authToken;

use InvalidArgumentException;

class RefreshToken extends AuthToken
{
    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('リフレッシュトークンが空です。');
        }
        parent::__construct($value);
    }
}