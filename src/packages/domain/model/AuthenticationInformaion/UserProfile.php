<?php

namespace packages\domain\model\authenticationInformaion;

use DateTimeImmutable;
use DomainException;
use packages\domain\service\AuthenticationInformaion\AuthenticationInformaionService;
// AuthenticationInformaion
class AuthenticationInformaion
{
    private UserId $userId;
    private UserEmail $userEmail;
    private UserName $userName;
    private UserPassword $userPassword;
    private VerificationStatus $verificationStatus;
    private LoginRestriction $LoginRestriction;

    private function __construct(
        UserId $userId,
        UserEmail $userEmail,
        UserName $userName,
        UserPassword $userPassword,
        VerificationStatus $verificationStatus,
        LoginRestriction $LoginRestriction
    )
    {
        $this->userId = $userId;
        $this->userEmail = $userEmail;
        $this->userName = $userName;
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
            UserName::initialization($userEmail),
            $userPassword,
            VerificationStatus::Unverified,
            LoginRestriction::initialization()
        );
    }

    public static function reconstruct(
        UserId $userId,
        UserEmail $userEmail,
        UserName $userName,
        UserPassword $userPassword,
        VerificationStatus $verificationStatus,
        LoginRestriction $LoginRestriction
    ): self
    {
        return new self(
            $userId,
            $userEmail,
            $userName,
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

    public function name(): UserName
    {
        return $this->userName;
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

    public function changeName(UserName $name, DateTimeImmutable $currentDateTime): void
    {
        if (!$this->isValid($currentDateTime)) {
            throw new DomainException('アカウントが有効ではありません。');
        }

        $this->userName = $name;
    }

    public function changePassword(UserPassword $password, DateTimeImmutable $currentDateTime): void
    {
        if (!$this->isValid($currentDateTime)) {
            throw new DomainException('アカウントが有効ではありません。');
        }

        $this->userPassword = $password;
    }

    /**
     * ログイン失敗回数を更新する
     */
    public function updateFailedLoginCount(): void
    {
        if (!$this->isVerified()) {
            throw new DomainException('認証済みのユーザーではありません。');
        }
        $this->LoginRestriction = $this->LoginRestriction->updateFailedLoginCount();
    }

    /**
     * 再ログイン可能な日時を更新する
     */
    public function updateNextLoginAt(): void
    {
        if (!$this->isVerified()) {
            throw new DomainException('認証済みのユーザーではありません。');
        }
        $this->LoginRestriction = $this->LoginRestriction->updateNextLoginAt();
    }

    /**
     * ログイン失敗回数がアカウントロックのしきい値に達したかどうかを判定
     */
    public function hasReachedAccountLockoutThreshold(): bool
    {
        return $this->LoginRestriction->hasReachedAccountLockoutThreshold();
    }

    public function isValid(DateTimeImmutable $currentDateTime): bool
    {
        return $this->isVerified() && !$this->isLocked($currentDateTime);
    }

    /**
     * アカウントがロックされているかどうかを判定
     */
    public function isLocked(DateTimeImmutable $currentDateTime): bool
    {
        return $this->LoginRestriction->isLoginAllowed($currentDateTime);
    }

    /**
     * 認証済みかどうかを判定
     */
    private function isVerified(): bool
    {
        return $this->verificationStatus->isVerified();
    }
}