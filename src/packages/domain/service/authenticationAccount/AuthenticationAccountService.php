<?php

namespace packages\domain\service\authenticationAccount;

use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserId;

class AuthenticationAccountService
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;

    public function __construct(IAuthenticationAccountRepository $authenticationAccountRepository)
    {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
    }

    /**
     * すでに存在するemailアドレスかどうかを判定
     */
    public function alreadyExistsEmail(UserEmail $email): bool
    {
        $authenticationAccount = $this->authenticationAccountRepository->findByEmail($email);

        return $authenticationAccount !== null;
    }
}