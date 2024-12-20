<?php

namespace packages\domain\model\authenticationAccount;

use InvalidArgumentException;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;

class UserId
{
    readonly string $value;

    public function __construct(string $value)
    {
        if (!IdentifierFromUUIDver7::isValidLength($value)) {
            throw new InvalidArgumentException('適切な文字列の長さではありません。');
        }

        if (!IdentifierFromUUIDver7::isValidFormat($value)) {
            throw new InvalidArgumentException('適切な形式になっていません。');
        }

        $this->value = $value;
    }

    public function equals(UserId $userId): bool
    {
        return $this->value === $userId->value;
    }
}