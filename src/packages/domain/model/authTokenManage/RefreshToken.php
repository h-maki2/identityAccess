<?php

namespace packages\domain\model\authTokenManage;

use DateTime;
use InvalidArgumentException;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\service\common\identifier\FetchElapsedTimeFromIdentifier;
use packages\domain\service\common\tokne\JwtTokenHandler;

class RefreshToken extends IdentifierFromUUIDver7
{
    readonly string $value;

    private function __construct(string $value)
    {
        if ($this->isValidLength($value)) {
            throw new InvalidArgumentException('適切な文字列の長さではありません。');
        }

        if (!$this->isValidFormat($value)) {
            throw new InvalidArgumentException('適切な形式になっていません。');
        }

        $this->value = $value;
    }

    public static function reconstructFromJwtToken(JwtTokenHandler $jwtTokenHandler, string $jwtToken): self
    {
        return new self($jwtTokenHandler->decode($jwtToken));
    }

    public static function create(string $value): self
    {
        return new self($value);
    }

    public function toJwtToken(JwtTokenHandler $jwtTokenHandler): string
    {
        return $jwtTokenHandler->encode($this->value, RefreshTokenExpirationDate::fromThreeMonths()->value);
    }
}