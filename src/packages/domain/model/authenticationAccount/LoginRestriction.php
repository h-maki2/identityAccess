<?php

namespace packages\domain\model\authenticationAccount;

use DateTimeImmutable;
use DomainException;

class LoginRestriction
{
    private FailedLoginCount $failedLoginCount;
    private LoginRestrictionStatus $loginRestrictionStatus;
    private ?NextLoginAllowedAt $nextLoginAllowedAt;

    private function __construct(
        FailedLoginCount $FailedLoginCount,
        LoginRestrictionStatus $loginRestrictionStatus,
        ?NextLoginAllowedAt $nextLoginAllowedAt
    )
    {
        $this->failedLoginCount = $FailedLoginCount;
        $this->loginRestrictionStatus = $loginRestrictionStatus;
        $this->nextLoginAllowedAt = $nextLoginAllowedAt;
    }

    public static function initialization(): self
    {
        return new self(
            FailedLoginCount::initialization(),
            LoginRestrictionStatus::Unrestricted,
            null
        );
    }

    public static function reconstruct(
        FailedLoginCount $FailedLoginCount,
        LoginRestrictionStatus $loginRestrictionStatus,
        ?NextLoginAllowedAt $nextLoginAllowedAt
    ): self
    {
        return new self(
            $FailedLoginCount,
            $loginRestrictionStatus,
            $nextLoginAllowedAt
        );
    }

    public function failedLoginCount(): int
    {
        return $this->failedLoginCount->value;
    }

    public function nextLoginAllowedAt(): ?string
    {
        if ($this->nextLoginAllowedAt === null) {
            return null;
        }

        return $this->nextLoginAllowedAt->formattedValue();
    }

    public function loginRestrictionStatus(): int
    {
        return $this->loginRestrictionStatus->value;
    }

    /**
     * ログイン失敗回数を更新する
     */
    public function addFailedLoginCount(): self
    {
        return new self(
            $this->failedLoginCount->add(),
            $this->loginRestrictionStatus,
            $this->nextLoginAllowedAt
        );
    }

    /**
     * ログイン制限を有効にする
     */
    public function enable(): self
    {
        if (!$this->canApply()) {
            throw new DomainException("ログイン失敗回数がログイン制限の回数に達していません。");
        }

        return new self(
            $this->failedLoginCount,
            LoginRestrictionStatus::Restricted,
            NextLoginAllowedAt::create()
        );
    }

    /**
     * ログイン制限を無効にする
     */
    public function disable(DateTimeImmutable $currentDateTime): self
    {
        if (!$this->canDisable($currentDateTime)) {
            throw new DomainException("ログイン制限が有効ではないか、もしくはログイン制限の期間内です。");
        }

        return new self(
            FailedLoginCount::initialization(),
            LoginRestrictionStatus::Unrestricted,
            null
        );
    }

    /**
     * ログイン制限が適用可能かどうかを判定
     */
    public function canApply(): bool
    {
        return $this->failedLoginCount->hasReachedLockoutThreshold();
    }

    /**
     * ログイン制限を無効にできるかどうかを判定
     */
    public function canDisable(DateTimeImmutable $currentDateTime): bool
    {
        if (!$this->isRestricted()) {
            return false;
        }
        
        return $this->nextLoginAllowedAt->isAvailable($currentDateTime);
    }

    /**
     * ログイン制限中かどうかを判定
     */
    public function isRestricted(): bool
    {
        return $this->loginRestrictionStatus->isRestricted();
    }
}