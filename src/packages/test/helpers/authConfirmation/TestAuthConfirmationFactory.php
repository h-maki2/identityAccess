<?php

namespace packages\test\helpers\authConfirmation;

use packages\domain\model\authConfirmation\AuthConfirmation;
use packages\domain\model\authConfirmation\OneTimePassword;
use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\AuthenticationInformation\UserId;
use packages\test\helpers\AuthenticationInformation\TestUserIdFactory;

class TestAuthConfirmationFactory
{
    public static function createAuthConfirmation(
        ?UserId $userId = null, 
        ?OneTimeToken $oneTimeToken = null, 
        ?OneTimePassword $oneTimePassword = null
    ): AuthConfirmation
    {
        return AuthConfirmation::reconstruct(
            $userId ?? TestUserIdFactory::createUserId(),
            $oneTimeToken ?? OneTimeToken::create(),
            $oneTimePassword ?? OneTimePassword::create()
        );
    }
}