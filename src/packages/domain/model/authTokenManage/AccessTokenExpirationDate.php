<?php

namespace packages\domain\model\authTokenManage;

class AccessTokenExpirationDate
{
    readonly int $value;

    public static function fromOneHour(): self
    {
        return new self(strtotime('+1 hour', time()));
    }
}