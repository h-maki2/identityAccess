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
use packages\domain\model\authenticationAccount\DefinitiveRegistrationCompletedStatus;

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
        ?DefinitiveRegistrationCompletedStatus $definitiveRegistrationCompletedStatus = null,
        ?UserId $id = null,
        ?LoginRestriction $loginRestriction = null,
        ?UnsubscribeStatus $unsubscribeStatus = null
    ): AuthenticationAccount
    {
        $authenticationAccount = TestAuthenticationAccountFactory::create(
            $email,
            $password,
            $definitiveRegistrationCompletedStatus,
            $id,
            $loginRestriction,
            $unsubscribeStatus
        );

        $this->authenticationAccountRepository->save($authenticationAccount);

        return $authenticationAccount;
    }
}