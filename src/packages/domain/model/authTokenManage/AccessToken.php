<?php

namespace packages\domain\model\authTokenManage;

use InvalidArgumentException;
use packages\domain\model\userProfile\UserId;
use packages\domain\service\common\tokne\JwtTokenHandler;

class AccessToken
{
    readonly string $value;

    private function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('accesstokenが空です。');
        }

        $this->value = $value;
    }

    public static function toJwtToken(JwtTokenHandler $jwtTokenHandler, UserId $userId): self
    {
        return new self(
            $jwtTokenHandler->encode(
                $userId->value,
                AccessTokenExpirationDate::fromOneHour()->value
            )
        );
    }

    public static function reconstruct(string $jwtToken): self
    {
        return new self($jwtToken);
    }

    public function toUserId(JwtTokenHandler $jwtTokenHandler): UserId
    {
        $userIdString = $jwtTokenHandler->decode($this->value);
        return new UserId($userIdString);
    }
}