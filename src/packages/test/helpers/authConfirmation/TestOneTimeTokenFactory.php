<?php

namespace packages\test\helpers\definitiveRegistrationConfirmation;

use packages\domain\model\definitiveRegistrationConfirmation\OneTimeToken;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenExpiration;
use packages\domain\model\definitiveRegistrationConfirmation\OneTimeTokenValue;

class TestOneTimeTokenFactory
{
    public static function createOneTimeToken(
        ?OneTimeTokenValue $tokenValue = null,
        ?OneTimeTokenExpiration $expiration = null
    ): OneTimeToken
    {
        $tokenValue = $tokenValue ?? OneTimeTokenValue::create();
        $expiration = $expiration ?? OneTimeTokenExpiration::create();
        return OneTimeToken::reconstruct($tokenValue, $expiration);
    }
}