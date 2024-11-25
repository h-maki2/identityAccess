<?php

namespace packages\domain\model\authenticationInformation;

use InvalidArgumentException;
use packages\domain\model\common\identifier\Identifier;

class UserId
{
    readonly string $value;

    public function __construct(Identifier $identifier, string $value)
    {
        if ($identifier->isValidLength($value)) {
            throw new InvalidArgumentException('適切な文字列の長さではありません。');
        }

        if (!$identifier->isValidFormat($value)) {
            throw new InvalidArgumentException('適切な形式になっていません。');
        }

        $this->value = $value;
    }
}