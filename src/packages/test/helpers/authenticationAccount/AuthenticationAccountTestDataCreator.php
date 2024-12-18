<?php

namespace packages\test\helpers\authenticationAccount;

use packages\domain\model\authenticationAccount\LoginRestriction;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\authenticationAccount\UserName;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\AuthenticationAccount;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationConfirmationStatus;

class AuthenticationAccountTestDataCreator
{
    private IAuthenticationAccountRepository $authenticationAccountRepository;

    public function __construct(IAuthenticationAccountRepository $authenticationAccountRepository)
    {
        $this->authenticationAccountRepository = $authenticationAccountRepository;
    }

    public function create(
        ?UserEmail $email = null,
        ?UserPassword $password = null,
        ?DefinitiveRegistrationConfirmationStatus $definitiveRegistrationConfirmationStatus = null,
        ?UserId $id = null,
        ?LoginRestriction $LoginRestriction = null,
        ?UnsubscribeStatus $unsubscribeStatus = null
    ): AuthenticationAccount
    {
        $authenticationAccount = TestAuthenticationAccountFactory::create(
            $email,
            $password,
            $definitiveRegistrationConfirmationStatus,
            $id,
            $LoginRestriction,
            $unsubscribeStatus
        );

        $this->authenticationAccountRepository->save($authenticationAccount);

        return $authenticationAccount;
    }
}