<?php

namespace packages\test\helpers\definitiveRegistrationConfirmation;

use packages\domain\model\definitiveRegistrationConfirmation\DefinitiveRegistrationConfirmation;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimePassword;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\authenticationAccount\UserId;
use packages\test\helpers\authenticationAccount\TestUserIdFactory;

class TestDefinitiveRegistrationConfirmationFactory
{
    public static function create(
        ?UserId $userId = null, 
        ?OneTimeToken $oneTimeToken = null, 
        ?OneTimePassword $oneTimePassword = null
    ): DefinitiveRegistrationConfirmation
    {
        return DefinitiveRegistrationConfirmation::reconstruct(
            $userId ?? TestUserIdFactory::createUserId(),
            $oneTimeToken ?? OneTimeToken::create(),
            $oneTimePassword ?? OneTimePassword::create()
        );
    }
}