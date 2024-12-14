<?php

namespace packages\test\helpers\authConfirmation;

use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;
use packages\domain\model\authConfirmation\OneTimeTokenValue;

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