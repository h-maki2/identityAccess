<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;

class UserId
{
    readonly string $value;

    private const USERID_LENGTH = 36;

    public function __construct(string $value)
    {
        if (strlen($value) !== self::USERID_LENGTH) {
            throw new InvalidArgumentException('ユーザーIDは36文字です。');
        }

        $this->value = $value;
    }
}