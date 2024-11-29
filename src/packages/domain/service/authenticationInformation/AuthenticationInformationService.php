<?php

namespace packages\domain\service\authenticationInformation;

use packages\domain\model\authenticationInformation\IAuthenticationInformationRepository;
use packages\domain\model\authenticationInformation\UserEmail;
use packages\domain\model\authenticationInformation\UserId;

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