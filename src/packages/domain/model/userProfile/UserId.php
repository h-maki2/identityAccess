<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;
use packages\domain\model\common\token\TokenFromUUIDver7;

class UserId extends TokenFromUUIDver7
{
    readonly string $value;

    public function __construct(string $value)
    {
        if ($this->isValidLength($value)) {
            throw new InvalidArgumentException('ユーザーIDは36文字です。');
        }

        if (!$this->isValidFormat($value)) {
            throw new InvalidArgumentException('UUID ver7の形式になっていません。');
        }

        $this->value = $value;
    }
}