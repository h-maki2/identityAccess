<?php

namespace packages\test\helpers\AuthenticationInformation;

use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\model\AuthenticationInformation\LoginRestriction;
use packages\domain\model\AuthenticationInformation\UserEmail;
use packages\domain\model\AuthenticationInformation\UserId;
use packages\domain\model\AuthenticationInformation\UserName;
use packages\domain\model\AuthenticationInformation\UserPassword;
use packages\domain\model\AuthenticationInformation\AuthenticationInformation;
use packages\domain\model\AuthenticationInformation\VerificationStatus;

class TestAuthenticationInformationFactory
{
    public static function create(
        ?UserEmail $email = null,
        ?UserPassword $password = null,
        ?VerificationStatus $verificationStatus = null,
        ?UserId $id = null,
        ?LoginRestriction $LoginRestriction = null
    ): AuthenticationInformation
    {
        return AuthenticationInformation::reconstruct(
            $id ?? new UserId(new IdentifierFromUUIDver7(), '0188b2a6-bd94-7ccf-9666-1df7e26ac6b8'),
            $email ?? new UserEmail('test@example.com'),
            $password ?? UserPassword::create('ABCabc123_'),
            $verificationStatus ?? VerificationStatus::Verified,
            $LoginRestriction ?? LoginRestriction::initialization()
        );
    }
}