<?php

namespace packages\domain\model\authenticationAccount;

use DateTimeImmutable;
use DomainException;
use Laravel\Passport\Exceptions\InvalidAuthTokenException;
use packages\domain\service\authenticationAccount\authenticationAccountService;

class AuthenticationAccount
{
    private UserId $userId;
    private UserEmail $userEmail;
    private UserPassword $userPassword;
    private VerificationStatus $verificationStatus;
    private LoginRestriction $loginRestriction;
    private UnsubscribeStatus $unsubscribeStatus;

    private function __construct(
        UserId $userId,
        UserEmail $userEmail,
        UserPassword $userPassword,
        VerificationStatus $verificationStatus,
        LoginRestriction $loginRestriction,
        UnsubscribeStatus $unsubscribeStatus
    )
    {
        if ($unsubscribeStatus->isUnsubscribed()) {
            throw new InvalidAuthTokenException('退会済みのユーザーです。');
        }

        $this->userId = $userId;
        $this->userEmail = $userEmail;
        $this->userPassword = $userPassword;
        $this->verificationStatus = $verificationStatus;
        $this->loginRestriction = $loginRestriction;
        $this->unsubscribeStatus = $unsubscribeStatus;
    }

    public static function create(
        UserId $userId,
        UserEmail $userEmail,
        UserPassword $userPassword,
        AuthenticationAccountService $authenticationAccountService
    ): self
    {
        $alreadyExistsEmail = $authenticationAccountService->alreadyExistsEmail($userEmail);
        if ($alreadyExistsEmail) {
            throw new DomainException('すでに存在するメールアドレスです。');
        }
        
        return new self(
            $userId,
            $userEmail,
            $userPassword,
            VerificationStatus::Unverified,
            LoginRestriction::initialization(),
            UnsubscribeStatus::Subscribed
        );
    }

    public static function reconstruct(
        UserId $userId,
        UserEmail $userEmail,
        UserPassword $userPassword,
        VerificationStatus $verificationStatus,
        LoginRestriction $LoginRestriction,
        UnsubscribeStatus $unsubscribeStatus
    ): self
    {
        return new self(
            $userId,
            $userEmail,
            $userPassword,
            $verificationStatus,
            $LoginRestriction,
            $unsubscribeStatus
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
        return $this->loginRestriction;
    }

    public function unsubscribeStatus(): UnsubscribeStatus
    {
        return $this->unsubscribeStatus;
    }

    public function updateVerified(): void
    {
        $this->verificationStatus = VerificationStatus::Verified;
    }

    public function updateUnsubscribed(): void
    {
        $this->unsubscribeStatus = UnsubscribeStatus::Unsubscribed;
    }

    public function changePassword(UserPassword $password, DateTimeImmutable $currentDateTime): void
    {
        if (!$this->isVerified()) {
            throw new DomainException('認証済みのユーザーではありません。');
        }

        if (!$this->canLoggedIn($currentDateTime)) {
            throw new DomainException('アカウントがロックされています。');
        }

        $this->userPassword = $password;
    }

    /**
     * ログイン失敗回数を更新する
     */
    public function addFailedLoginCount(): void
    {
        if (!$this->isVerified()) {
            throw new DomainException('認証済みのユーザーではありません。');
        }

        $this->loginRestriction = $this->loginRestriction->addFailedLoginCount();
    }

    /**
     * ログイン制限を有効にする
     */
    public function locking(DateTimeImmutable $currentDateTime): void
    {
        if (!$this->isVerified()) {
            throw new DomainException('認証済みのユーザーではありません。');
        }

        $this->loginRestriction = $this->loginRestriction->enable($currentDateTime);
    }

    /**
     * ログイン制限を無効にする
     */
    public function unlocking(DateTimeImmutable $currentDateTime): void
    {
        if (!$this->isVerified()) {
            throw new DomainException('認証済みのユーザーではありません。');
        }

        $this->loginRestriction = $this->loginRestriction->disable($currentDateTime);
    }

    /**
     * ログイン可能かどうかを判定
     */
    public function canLoggedIn(DateTimeImmutable $currentDateTime): bool
    {
        if (!$this->isVerified()) {
            return false;
        }

        if (!$this->loginRestriction->isRestricted()) {
            return true;
        }

        if ($this->canUnlocking($currentDateTime)) {
            return true;
        }

        return false;
    }

    /**
     * ログイン制限を無効にできるかどうかを判定
     */
    public function canUnlocking(DateTimeImmutable $currentDateTime): bool
    {
        return $this->loginRestriction->canDisable($currentDateTime);
    }

    /**
     * 認証済みかどうかを判定
     */
    public function isVerified(): bool
    {
        return $this->verificationStatus->isVerified();
    }

    /**
     * ログイン制限を有効にできるかどうかを判定する
     */
    public function canLocking(): bool
    {
        return $this->loginRestriction->canApply();
    }
}