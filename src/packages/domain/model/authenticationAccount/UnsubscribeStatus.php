<?php

namespace packages\domain\model\authenticationAccount;

enum UnsubscribeStatus: int
{
    case Unsubscribed = 0;
    case Subscribed = 1;

    /**
     * 退会済みかどうかを判定
     */
    public function isUnsubscribed(): bool
    {
        return $this === self::Unsubscribed;
    }
}