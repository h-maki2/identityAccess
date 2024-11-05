<?php

namespace packages\domain\model\userProfile;

use DomainException;

class AuthenticationLimitation
{
    private FailedLoginCount $FailedLoginCount;
    private ?NextLoginAt $nextLoginAt;

    private function __construct(
        FailedLoginCount $FailedLoginCount,
        ?NextLoginAt $nextLoginAt
    )
    {
        $this->FailedLoginCount = $FailedLoginCount;
        $this->nextLoginAt = $nextLoginAt;
    }

    public static function initialization(): self
    {
        return new self(
            FailedLoginCount::initialization(),
            null
        );
    }

    public static function reconstruct(
        FailedLoginCount $FailedLoginCount,
        ?NextLoginAt $nextLoginAt
    ): self
    {
        return new self(
            $FailedLoginCount,
            $nextLoginAt
        );
    }

    public function failedLoginCount(): int
    {
        return $this->FailedLoginCount->value;
    }

    public function nextLoginAt(): ?string
    {
        if ($this->nextLoginAt === null) {
            return null;
        }

        return $this->nextLoginAt->formattedValue();
    }

    /**
     * ログイン失敗回数を更新する
     */
    public function updateFailedLoginCount(): self
    {
        return new self(
            $this->FailedLoginCount->add(),
            $this->nextLoginAt
        );
    }

    /**
     * 再ログイン可能な日時を設定する
     */
    public function updateNextLoginAt(): self
    {
        if (!$this->hasReachedAccountLockoutThreshold()) {
            throw new DomainException("ログイン失敗回数がアカウントロックの回数に達していません。");
        }

        return new self(
            $this->FailedLoginCount,
            NextLoginAt::create()
        );
    }

    /**
     * ログイン失敗回数がアカウントロックのしきい値に達したかどうかを判定
     */
    public function hasReachedAccountLockoutThreshold(): bool
    {
        return $this->FailedLoginCount->hasReachedLockoutThreshold();
    }
}