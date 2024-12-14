<?php

namespace packages\test\helpers\authenticationInformation;

use packages\domain\model\authenticationInformation\LoginRestriction;
use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\authenticationInformation\UserName;
use packages\domain\model\authenticationInformation\UserPassword;
use packages\domain\model\authenticationInformation\AuthenticationInformation;
use packages\domain\model\authenticationInformation\VerificationStatus;

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