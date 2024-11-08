<?php

namespace packages\domain\service\AuthenticationInformaion;

use packages\domain\model\authenticationInformaion\IAuthenticationInformaionRepository;
use packages\domain\model\authenticationInformaion\UserEmail;

class AuthenticationInformaionService
{
    private IAuthenticationInformaionRepository $authenticationInformaionRepository;

    public function __construct(IAuthenticationInformaionRepository $authenticationInformaionRepository)
    {
        $this->AuthenticationInformaionRepository = $authenticationInformaionRepository;
    }

    /**
     * すでに存在するemailアドレスかどうかを判定
     */
    public function alreadyExistsEmail(UserEmail $email): bool
    {
        $authenticationInformaion = $this->AuthenticationInformaionRepository->findByEmail($email);

        return $authenticationInformaion !== null;
    }
}