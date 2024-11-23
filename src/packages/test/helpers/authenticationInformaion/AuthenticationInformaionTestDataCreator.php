<?php

namespace packages\test\helpers\AuthenticationInformation;

use packages\domain\model\AuthenticationInformation\LoginRestriction;
use packages\domain\model\AuthenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\AuthenticationInformation\UserEmail;
use packages\domain\model\AuthenticationInformation\UserId;
use packages\domain\model\AuthenticationInformation\UserName;
use packages\domain\model\AuthenticationInformation\UserPassword;
use packages\domain\model\AuthenticationInformation\AuthenticationInformation;
use packages\domain\model\AuthenticationInformation\VerificationStatus;

class AuthenticationInformationTestDataCreator
{
    private IAuthenticationInformationRepository $authenticationInformationRepository;

    public function __construct(IAuthenticationInformationRepository $authenticationInformationRepository)
    {
        $this->authenticationInformationRepository = $authenticationInformationRepository;
    }

    public function create(
        ?UserEmail $email = null,
        ?UserPassword $password = null,
        ?VerificationStatus $verificationStatus = null,
        ?UserId $id = null,
        ?LoginRestriction $LoginRestriction = null
    ): AuthenticationInformation
    {
        $authenticationInformation = TestAuthenticationInformationFactory::create(
            $email,
            $password,
            $verificationStatus,
            $id,
            $LoginRestriction
        );

        $this->authenticationInformationRepository->save($authenticationInformation);

        return $authenticationInformation;
    }
}