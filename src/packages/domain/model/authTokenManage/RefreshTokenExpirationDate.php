<?php

namespace packages\domain\model\authTokenManage;

class RefreshTokenExpirationDate
{
    readonly int $value;

    public static function fromOneHour(): self
    {
        return new self(strtotime('+3 months', time()));
    }
}