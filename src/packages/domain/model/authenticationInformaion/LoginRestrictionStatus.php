<?php

namespace packages\domain\model\authenticationInformaion;

enum LoginRestrictionStatus: int
{
    case Unrestricted = 0;
    case Restricted = 1;

    /**
     * ログイン制限されているかどうかを判定
     */
    public function isRestricted(): bool
    {
        return $this->value === self::Restricted->value;
    }
}