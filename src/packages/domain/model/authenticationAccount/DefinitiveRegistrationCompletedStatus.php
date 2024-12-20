<?php

namespace packages\domain\model\authenticationAccount;

enum DefinitiveRegistrationCompletedStatus: string
{
    case Completed = '1';
    case Incomplete = '0';

    /**
     * 本登録済みかどうかを判定
     */
    public function isCompleted(): bool
    {
        return $this === self::Completed;
    }
}