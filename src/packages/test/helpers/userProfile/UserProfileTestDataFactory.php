<?php

namespace packages\test\helpers\AuthenticationInformaion;

use packages\domain\model\authenticationInformaion\LoginRestriction;
use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\UserEmail;
use packages\domain\model\authenticationInformaion\UserId;
use packages\domain\model\authenticationInformaion\UserName;
use packages\domain\model\authenticationInformaion\UserPassword;
use packages\domain\model\authenticationInformaion\AuthenticationInformaion;
use packages\domain\model\authenticationInformaion\VerificationStatus;

class AuthenticationInformaionTestDataFactory
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;

    public function __construct(IAuthenticationInformaionRepository $authenticationInformaionRepository)
    {
        $this->AuthenticationInformaionRepository = $authenticationInformaionRepository;
    }

    public function create(
        ?UserEmail $email = null,
        ?UserName $name = null,
        ?UserPassword $password = null,
        ?VerificationStatus $verificationStatus = null,
        ?UserId $id = null,
        ?LoginRestriction $LoginRestriction = null
    ): AuthenticationInformaion
    {
        $authenticationInformaion = TestAuthenticationInformaionFactory::create(
            $email,
            $name,
            $password,
            $verificationStatus,
            $id,
            $LoginRestriction
        );

        $this->AuthenticationInformaionRepository->save($authenticationInformaion);

        return $authenticationInformaion;
    }
}