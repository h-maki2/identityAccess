<?php

namespace packages\domain\service\authentication;

use DateTimeImmutable;
use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\UserProfile;

class LoginFailureManager
{
    private IUserProfileRepository $userProfileRepository;

    public function __construct(IUserProfileRepository $userProfileRepository)
    {
        $this->userProfileRepository = $userProfileRepository;
    }

    /**
     * ログイン失敗時の処理
     */
    public function handleFailedLoginAttempt(?UserProfile $userProfile, DateTimeImmutable $currentDateTime): void
    {
        if ($userProfile === null) {
            return;
        }

        if (!$userProfile->isVerified()) {
            return;
        }

        if ($userProfile->isLocked($currentDateTime)) {
            return;
        }

        $userProfile->updateFailedLoginCount();
        if ($userProfile->hasReachedAccountLockoutThreshold()) {
            $userProfile->updateNextLoginAt();
        }
        $this->userProfileRepository->save($userProfile);
    }
}