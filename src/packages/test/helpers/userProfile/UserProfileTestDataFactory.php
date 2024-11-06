<?php

namespace packages\test\helpers\userProfile;

use packages\domain\model\userProfile\AuthenticationLimitation;
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
        ?UserEmail $email = null,
        ?UserName $name = null,
        ?UserPassword $password = null,
        ?VerificationStatus $verificationStatus = null,
        ?UserId $id = null,
        ?AuthenticationLimitation $authenticationLimitation = null
    ): UserProfile
    {
        $userProfile = TestUserProfileFactory::create(
            $email,
            $name,
            $password,
            $verificationStatus,
            $id,
            $authenticationLimitation
        );

        $this->userProfileRepository->save($userProfile);

        return $userProfile;
    }
}