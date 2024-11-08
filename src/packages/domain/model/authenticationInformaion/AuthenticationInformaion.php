<?php

namespace packages\domain\model\authenticationInformaion;

use DateTimeImmutable;
use DomainException;
use packages\domain\service\AuthenticationInformaion\AuthenticationInformaionService;

class AuthenticationInformaion
{
    private UserId $userId;
    private UserEmail $userEmail;
    private UserPassword $userPassword;
    private VerificationStatus $verificationStatus;
    private LoginRestriction $LoginRestriction;

    private function __construct(
        UserId $userId,
        UserEmail $userEmail,
        UserPassword $userPassword,
        VerificationStatus $verificationStatus,
        LoginRestriction $LoginRestriction
    )
    {
        $this->userId = $userId;
        $this->userEmail = $userEmail;
        $this->userPassword = $userPassword;
        $this->verificationStatus = $verificationStatus;
        $this->LoginRestriction = $LoginRestriction;
    }

    public static function create(
        UserId $userId,
        UserEmail $userEmail,
        UserPassword $userPassword,
        AuthenticationInformaionService $authenticationInformaionService
    ): self
    {
        $alreadyExistsEmail = $authenticationInformaionService->alreadyExistsEmail($userEmail);
        if ($alreadyExistsEmail) {
            throw new DomainException('すでに存在するメールアドレスです。');
        }
        
        return new self(
            $userId,
            $userEmail,
            $userPassword,
            VerificationStatus::Unverified,
            LoginRestriction::initialization()
        );
    }

    public static function reconstruct(
        UserId $userId,
        UserEmail $userEmail,
        UserPassword $userPassword,
        VerificationStatus $verificationStatus,
        LoginRestriction $LoginRestriction
    ): self
    {
        return new self(
            $userId,
            $userEmail,
            $userPassword,
            $verificationStatus,
            $LoginRestriction
        );
    }

    public function id(): UserId
    {
        return $this->userId;
    }

    public function email(): UserEmail
    {
        return $this->userEmail;
    }

    public function password(): UserPassword
    {
        return $this->userPassword;
    }

    public function verificationStatus(): VerificationStatus
    {
        return $this->verificationStatus;
    }

    public function LoginRestriction(): LoginRestriction
    {
        return $this->LoginRestriction;
    }

    public function updateVerified(): void
    {
        $this->verificationStatus = VerificationStatus::Verified;
    }

    public function changePassword(UserPassword $password, DateTimeImmutable $currentDateTime): void
    {
        if ($this->isLocked($currentDateTime)) {
            throw new DomainException('アカウントがロックされています。');
        }

        if (!$this->isVerified()) {
            throw new DomainException('認証済みのユーザーではありません。');
        }

        $this->userPassword = $password;
    }

    /**
     * ログイン失敗回数を更新する
     */
    public function updateFailedLoginCount(DateTimeImmutable $currentDateTime): void
    {
        if (!$this->isVerified()) {
            return;
        }

        if ($this->isLocked($currentDateTime)) {
            return;
        }
        $this->LoginRestriction = $this->LoginRestriction->updateFailedLoginCount();
    }

    /**
     * ログイン制限を有効にする
     */
    public function enableLoginRestriction(DateTimeImmutable $currentDateTime): void
    {
        if ($this->isVerified()) {
            return;
        }

        if ($this->isLocked($currentDateTime)) {
            return;
        }

        if (!$this->canApplyLoginRestriction()) {
            return;
        }

        $this->LoginRestriction = $this->LoginRestriction->enable();
    }

    /**
     * ログイン制限を無効にする
     */
    public function disableLoginRestriction(DateTimeImmutable $currentDateTime): void
    {
        if (!$this->isVerified()) {
            return;
        }

        if ($this->isLocked($currentDateTime)) {
            return;
        }

        $this->LoginRestriction = $this->LoginRestriction->disable($currentDateTime);
    }

    /**
     * 認証情報がロックされているかどうかを判定
     */
    public function isLocked(DateTimeImmutable $currentDateTime): bool
    {
        return $this->LoginRestriction->isEnable($currentDateTime);
    }

    /**
     * 認証済みかどうかを判定
     */
    public function isVerified(): bool
    {
        return $this->verificationStatus->isVerified();
    }

    /**
     * ログイン制限が有効可能かどうかを判定
     */
    private function canApplyLoginRestriction(): bool
    {
        return $this->LoginRestriction->canApply();
    }
}