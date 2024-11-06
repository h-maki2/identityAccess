<?php

namespace packages\domain\model\userProfile;

use DomainException;
use packages\domain\service\userProfile\UserProfileService;

class UserProfile
{
    private UserId $userId;
    private UserEmail $userEmail;
    private UserName $userName;
    private UserPassword $userPassword;
    private VerificationStatus $verificationStatus;
    private AuthenticationLimitation $authenticationLimitation;

    private function __construct(
        UserId $userId,
        UserEmail $userEmail,
        UserName $userName,
        UserPassword $userPassword,
        VerificationStatus $verificationStatus,
        AuthenticationLimitation $authenticationLimitation
    )
    {
        $this->userId = $userId;
        $this->userEmail = $userEmail;
        $this->userName = $userName;
        $this->userPassword = $userPassword;
        $this->verificationStatus = $verificationStatus;
        $this->authenticationLimitation = $authenticationLimitation;
    }

    public static function create(
        UserId $userId,
        UserEmail $userEmail,
        UserPassword $userPassword,
        UserProfileService $userProfileService
    ): self
    {
        $alreadyExistsEmail = $userProfileService->alreadyExistsEmail($userEmail);
        if ($alreadyExistsEmail) {
            throw new DomainException('すでに存在するメールアドレスです。');
        }
        
        return new self(
            $userId,
            $userEmail,
            UserName::initialization($userEmail),
            $userPassword,
            VerificationStatus::Unverified,
            AuthenticationLimitation::initialization()
        );
    }

    public static function reconstruct(
        UserId $userId,
        UserEmail $userEmail,
        UserName $userName,
        UserPassword $userPassword,
        VerificationStatus $verificationStatus,
        AuthenticationLimitation $authenticationLimitation
    ): self
    {
        return new self(
            $userId,
            $userEmail,
            $userName,
            $userPassword,
            $verificationStatus,
            $authenticationLimitation
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

    public function authenticationLimitation(): AuthenticationLimitation
    {
        return $this->authenticationLimitation;
    }

    public function updateVerified(): void
    {
        $this->verificationStatus = VerificationStatus::Verified;
    }

    public function changeName(UserName $name): void
    {
        if (!$this->isVerified()) {
            throw new DomainException('認証済みのユーザーではありません。');
        }

        $this->userName = $name;
    }

    public function changePassword(UserPassword $password): void
    {
        if (!$this->isVerified()) {
            throw new DomainException('認証済みのユーザーではありません。');
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
        $this->authenticationLimitation = $this->authenticationLimitation->updateFailedLoginCount();
    }

    /**
     * 再ログイン可能な日時を更新する
     */
    public function updateNextLoginAt(): void
    {
        if (!$this->isVerified()) {
            throw new DomainException('認証済みのユーザーではありません。');
        }
        $this->authenticationLimitation = $this->authenticationLimitation->updateNextLoginAt();
    }

    /**
     * ログイン失敗回数がアカウントロックのしきい値に達したかどうかを判定
     */
    public function hasReachedAccountLockoutThreshold(): bool
    {
        return $this->authenticationLimitation->hasReachedAccountLockoutThreshold();
    }

    /**
     * 認証済みかどうかを判定
     */
    public function isVerified(): bool
    {
        return $this->verificationStatus->isVerified();
    }
}