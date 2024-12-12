<?php

namespace packages\test\helpers\authenticationInformation;

use packages\adapter\persistence\inMemory\InMemoryAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\LoginRestriction;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\AuthenticationInformation;
use packages\domain\model\authenticationInformation\VerificationStatus;

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
        $authInfoRepository = new InMemoryAuthenticationInformationRepository();
        return AuthenticationInformation::reconstruct(
            $id ?? $authInfoRepository->nextUserId(),
            $email ?? TestUserEmailFactory::create(),
            $password ?? UserPassword::create('ABCabc123_'),
            $verificationStatus ?? VerificationStatus::Verified,
            $LoginRestriction ?? LoginRestriction::initialization()
        );
    }
}