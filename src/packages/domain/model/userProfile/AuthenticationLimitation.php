<?php

namespace packages\domain\model\userProfile;

use DomainException;

class AuthenticationLimitation
{
    private FailedLoginAttempts $failedLoginAttempts;
    private ?NextLoginAt $nextLoginAt;

    private function __construct(
        FailedLoginAttempts $failedLoginAttempts,
        ?NextLoginAt $nextLoginAt
    )
    {
        $this->failedLoginAttempts = $failedLoginAttempts;
        $this->nextLoginAt = $nextLoginAt;
    }

    public static function initialization(): self
    {
        return new self(
            FailedLoginAttempts::initialization(),
            null
        );
    }

    public static function reconstruct(
        FailedLoginAttempts $failedLoginAttempts,
        ?NextLoginAt $nextLoginAt
    ): self
    {
        return new self(
            $failedLoginAttempts,
            $nextLoginAt
        );
    }

    public function updateFailedLoginAttempts(): self
    {
        return new self(
            $this->failedLoginAttempts->add(),
            $this->nextLoginAt
        );
    }

    /**
     * 再ログイン可能な日時を更新する
     */
    public function updateNextLoginAt(): self
    {
        if (!$this->hasReachedAccountLockoutThreshold()) {
            throw new DomainException("ログイン失敗回数がアカウントロックの回数に達していません。");
        }

        return new self(
            $this->failedLoginAttempts,
            NextLoginAt::create()
        );
    }

    /**
     * ログイン失敗回数がアカウントロックのしきい値に達したかどうかを判定
     */
    public function hasReachedAccountLockoutThreshold(): bool
    {
        return $this->failedLoginAttempts->hasReachedLockoutThreshold();
    }
}