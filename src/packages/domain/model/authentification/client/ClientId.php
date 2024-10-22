<?php

namespace packages\domain\model\authentification\client;

use InvalidArgumentException;
use packages\domain\model\common\identifier\Identifier;

class ClientId
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