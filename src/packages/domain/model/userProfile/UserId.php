<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;

class UserId
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('ユーザーIDが空です。');
        }

        if (strlen($value) !== 26) {
            throw new InvalidArgumentException('ユーザーIDは26文字です。');
        }

        $this->value = $value;
    }
}