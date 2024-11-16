<?php

namespace packages\test\helpers\authConfirmation;

use packages\domain\model\authConfirmation\OneTimeToken;
use packages\domain\model\authConfirmation\OneTimeTokenExpiration;

class TestOneTimeTokenFactory
{
    public static function createOneTimeToken(
        ?string $tokenValue = null,
        ?OneTimeTokenExpiration $expiration = null
    ): OneTimeToken
    {
        $tokenValue = $tokenValue ?? OneTimeToken::create()->value;
        $expiration = $expiration ?? OneTimeTokenExpiration::create();
        return OneTimeToken::reconstruct($tokenValue, $expiration);
    }
}