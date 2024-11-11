<?php

namespace packages\domain\service\authenticationInformaion;

use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\UserEmail;

class AuthenticationInformaionService
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;

    public function __construct(IAuthenticationInformaionRepository $authenticationInformaionRepository)
    {
        $this->authenticationInformaionRepository = $authenticationInformaionRepository;
    }

    /**
     * すでに存在するemailアドレスかどうかを判定
     */
    public function alreadyExistsEmail(UserEmail $email): bool
    {
        $authenticationInformaion = $this->authenticationInformaionRepository->findByEmail($email);

        return $authenticationInformaion !== null;
    }
}