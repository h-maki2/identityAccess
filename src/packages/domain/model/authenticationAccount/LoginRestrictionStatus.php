<?php

namespace packages\domain\model\authenticationAccount;

enum LoginRestrictionStatus: int
{
    case Unrestricted = 0;
    case Restricted = 1;

    /**
     * ログイン制限されているかどうかを判定
     */
    public function isRestricted(): bool
    {
        return $this === self::Restricted;
    }
}