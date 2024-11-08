<?php

namespace packages\test\helpers\AuthenticationInformaion;

use packages\domain\model\common\identifier\IdentifierFromUUIDver7;
use packages\domain\model\authenticationInformaion\LoginRestriction;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\UserId;
use packages\domain\model\authenticationInformaion\UserName;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\VerificationStatus;

class TestAuthenticationInformaionFactory
{
    public static function create(
        ?UserEmail $email = null,
        ?UserName $name = null,
        ?UserPassword $password = null,
        ?VerificationStatus $verificationStatus = null,
        ?UserId $id = null,
        ?LoginRestriction $LoginRestriction = null
    ): AuthenticationInformaion
    {
        return AuthenticationInformaion::reconstruct(
            $id ?? new UserId(new IdentifierFromUUIDver7(), '0188b2a6-bd94-7ccf-9666-1df7e26ac6b8'),
            $email ?? new UserEmail('test@example.com'),
            $name ?? UserName::create('testUser'),
            $password ?? UserPassword::create('ABCabc123_'),
            $verificationStatus ?? VerificationStatus::Verified,
            $LoginRestriction ?? LoginRestriction::initialization()
        );
    }
}