<?php

namespace packages\domain\model\userProfile;

use InvalidArgumentException;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;

class UserProfileId
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
}