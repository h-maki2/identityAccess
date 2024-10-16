<?php

namespace packages\domain\model\userProfile;

enum VerificationStatus: string
{
    case Verified = '1';
    case Unverified = '0';
    
    public function displayValue(): string
    {
        return match($this) {
            self::Verified => '認証済み',
            self::Unverified => '未認証'
        };
    }

    /**
     * 認証済みかどうかを判定
     */
    public function isVerified(): bool
    {
        return $this->value === self::Verified->value;
    }
}