<?php

namespace packages\domain\service\userProfile;

use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\UserEmail;

class UserProfileService
{
    private IUserProfileRepository $userProfileRepository;

    public function __construct(IUserProfileRepository $userProfileRepository)
    {
        $this->userProfileRepository = $userProfileRepository;
    }

    /**
     * すでに存在するemailアドレスかどうかを判定
     */
    public function alreadyExistsEmail(UserEmail $email): bool
    {
        $userProfile = $this->userProfileRepository->findByEmail($email);

        return $userProfile !== null;
    }
}