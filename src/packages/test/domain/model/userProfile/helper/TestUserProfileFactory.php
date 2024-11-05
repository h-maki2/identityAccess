<?php

namespace packages\test\domain\model\userProfile\helper;

use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\model\userProfile\AuthenticationLimitation;
use packages\domain\model\userProfile\UserEmail;
use packages\domain\model\userProfile\UserId;
use packages\domain\model\userProfile\UserName;
use packages\domain\model\userProfile\UserPassword;
use packages\domain\model\userProfile\UserProfile;
use packages\domain\model\userProfile\VerificationStatus;

class TestUserProfileFactory
{
    public static function create(
        ?UserEmail $email = null,
        ?UserName $name = null,
        ?UserPassword $password = null,
        ?VerificationStatus $verificationStatus = null,
        ?UserId $id = null,
        ?AuthenticationLimitation $authenticationLimitation = null
    ): UserProfile
    {
        return UserProfile::reconstruct(
            $id ?? new UserId(new IdentifierFromUUIDver7(), '0188b2a6-bd94-7ccf-9666-1df7e26ac6b8'),
            $email ?? new UserEmail('test@example.com'),
            $name ?? UserName::create('testUser'),
            $password ?? UserPassword::create('ABCabc123_'),
            $verificationStatus ?? VerificationStatus::Verified,
            $authenticationLimitation ?? AuthenticationLimitation::initialization()
        );
    }
}