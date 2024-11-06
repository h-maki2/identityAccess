<?php

namespace packages\domain\service\auth;

use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\UserEmail;
use packages\domain\model\userProfile\UserProfile;

class UserAuthenticator
{
    private IUserProfileRepository $userProfileRepository;

    public function __construct(IUserProfileRepository $userProfileRepository)
    {
        $this->userProfileRepository = $userProfileRepository;
    }

    public function login(UserEmail $email, string $password): ?UserProfile
    {
        $userProfile = $this->userProfileRepository->findByEmail($email);
        if (!$this->validateCredentials($userProfile, $password)) {
            return null;
        }

        return $userProfile;
    }

    private function validateCredentials(?UserProfile $userProfile, string $password): bool
    {
        if ($userProfile === null) {
            return false;
        }

        if (!$userProfile->isVerified()) {
            return false;
        }

        if ($userProfile->hasReachedAccountLockoutThreshold()) {
            return false;
        }

        if (!$userProfile->password()->equals($password)) {
            return false;
        }

        return true;
    }
}