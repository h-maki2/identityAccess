<?php

namespace packages\test\domain\model\userProfile\helper;

use packages\domain\model\userProfile\UserEmail;
use packages\domain\model\userProfile\UserId;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserPassword;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\VerificationStatus;

class TestUserProfileFactory
{
    public static function create(
        ?UserName $name = null,
        ?UserPassword $password = null,
        ?VerificationStatus $verificationStatus = null,
        ?UserEmail $email = null,
        ?UserId $id = null
    ): UserProfile
    {
        return UserProfile::reconstruct(
            $id ?? new UserId('01FVSHW3S99VWCKTQVG1EQB6CM'),
            $email ?? new UserEmail('test@example.com'),
            $name ?? UserName::reconstruct('testUser'),
            $password ?? UserPassword::reconstruct('ABCabc123_'),
            $verificationStatus ?? VerificationStatus::Verified
        );
    }
}