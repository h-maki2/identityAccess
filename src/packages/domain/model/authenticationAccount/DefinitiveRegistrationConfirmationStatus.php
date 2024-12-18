<?php

namespace packages\domain\model\authenticationAccount;

enum definitiveRegistrationConfirmationStatus: string
{
    case Verified = '1';
    case Unverified = '0';

    /**
     * 本登録済みかどうかを判定
     */
    public function isVerified(): bool
    {
        return $this === self::Verified;
    }
}