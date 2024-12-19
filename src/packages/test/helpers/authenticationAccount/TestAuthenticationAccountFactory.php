<?php

namespace packages\test\helpers\authenticationAccount;

use packages\adapter\persistence\inMemory\InMemoryAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\LoginRestriction;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\authenticationAccount\AuthenticationAccount;
use packages\domain\model\authenticationAccount\UnsubscribeStatus;
use packages\domain\model\authenticationAccount\DefinitiveRegistrationCompletedStatus;

class TestAuthenticationAccountFactory
{
    public static function create(
        ?UserEmail $email = null,
        ?UserPassword $password = null,
        ?DefinitiveRegistrationCompletedStatus $definitiveRegistrationCompletedStatus = null,
        ?UserId $id = null,
        ?LoginRestriction $loginRestriction = null,
        ?UnsubscribeStatus $unsubscribeStatus = null
    ): AuthenticationAccount
    {
        $authInfoRepository = new InMemoryAuthenticationAccountRepository();
        return AuthenticationAccount::reconstruct(
            $id ?? $authInfoRepository->nextUserId(),
            $email ?? TestUserEmailFactory::create(),
            $password ?? UserPassword::create('ABCabc123_'),
            $definitiveRegistrationCompletedStatus ?? definitiveRegistrationCompletedStatus::Completed,
            $loginRestriction ?? LoginRestriction::initialization(),
            $unsubscribeStatus ?? UnsubscribeStatus::Subscribed
        );
    }
}