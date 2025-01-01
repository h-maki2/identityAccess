<?php

namespace packages\test\helpers\oauth\authToken;

use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\RefreshToken;

class AuthTokenTestData
{
    readonly AccessToken $accessToken;
    readonly RefreshToken $refreshToken;

    public function __construct(
        AccessToken $accessToken,
        RefreshToken $refreshToken
    )
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }
}