<?php

namespace packages\domain\model\authenticationAccount;

enum VerificationStatus: string
{
    case Verified = '1';
    case Unverified = '0';
    
    public function displayValue(): string
    {
        return match($this) {
            self::Verified => '本登録済み',
            self::Unverified => '未確認'
        };
    }

    /**
     * 本登録済みかどうかを判定
     */
    public function isVerified(): bool
    {
        return $this === self::Verified;
    }
}