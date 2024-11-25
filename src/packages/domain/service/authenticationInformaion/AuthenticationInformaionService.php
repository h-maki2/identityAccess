<?php

namespace packages\domain\service\AuthenticationInformation;

use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;

class AuthenticationInformationService
{
    private IAuthenticationInformationRepository $authenticationInformationRepository;

    public function __construct(IAuthenticationInformationRepository $authenticationInformationRepository)
    {
        $this->authenticationInformationRepository = $authenticationInformationRepository;
    }

    /**
     * すでに存在するemailアドレスかどうかを判定
     */
    public function alreadyExistsEmail(UserEmail $email): bool
    {
        $authenticationInformation = $this->authenticationInformationRepository->findByEmail($email);

        return $authenticationInformation !== null;
    }
}