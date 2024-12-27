<?php

namespace packages\adapter\oauth\authToken;

use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\IAccessTokenCookieService;

class LaravelAccessTokenCookieService implements IAccessTokenCookieService
{
    public function fetch(): ?AccessToken
    {
        $accessTokenString = cookie('access_token');
        if ($accessTokenString === null) {
            return null;
        }
        return new AccessToken($accessTokenString);
    }
}