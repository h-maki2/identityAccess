<?php

namespace packages\test\domain\model\userProfile\helper;

use packages\domain\model\userProfile\IUserProfileRepository;
use packages\domain\model\userProfile\UserEmail;
use packages\domain\model\userProfile\UserId;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserPassword;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\VerificationStatus;

class UserProfileTestDataFactory
{
    private IUserProfileRepository $userProfileRepository;

    public function __construct(IUserProfileRepository $userProfileRepository)
    {
        $this->userProfileRepository = $userProfileRepository;
    }

    public function create(
        ?UserName $name = null,
        ?UserPassword $password = null,
        VerificationStatus $verificationStatus = VerificationStatus::Verified,
        UserEmail $email = new UserEmail('test@example.com'),
        UserId $id = new UserId('0188b2a6-bd94-7ccf-9666-1df7e26ac6b8')
    ): UserProfile
    {
        $userProfile = TestUserProfileFactory::create(
            $name,
            $password,
            $verificationStatus,
            $email,
            $id
        );

        $this->userProfileRepository->save($userProfile);

        return $userProfile;
    }
}