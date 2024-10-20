<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;

class UserId extends IdentifierFromUUIDver7
{
    readonly string $value;

    public function __construct(string $value)
    {
        if ($this->isValidLength($value)) {
            throw new InvalidArgumentException('適切な文字列の長さではありません。');
        }

        if (!$this->isValidFormat($value)) {
            throw new InvalidArgumentException('適切な形式になっていません。');
        }

        $this->value = $value;
    }
}