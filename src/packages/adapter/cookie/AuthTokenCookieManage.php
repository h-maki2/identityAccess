<?php

namespace packages\adapter\cookie;

use packages\domain\model\authTokenManage\IAuthTokenCookieManage;
use Illuminate\Support\Facades\Cookie;
use packages\adapter\token\FirebaseJwtTokenHandler;
use packages\domain\model\authTokenManage\AccessToken;
use packages\domain\model\authTokenManage\AccessTokenExpirationDate;
use packages\domain\model\authTokenManage\RefreshToken;
use packages\domain\model\authTokenManage\RefreshTokenExpirationDate;

class AuthTokenCookieManage implements IAuthTokenCookieManage
{
    public function storeAccessToken(AccessToken $accessToken, AccessTokenExpirationDate $accessTokenExpirationDate): void
    {
        Cookie::make('accessToken', $accessToken->value, $accessTokenExpirationDate->value, '/', config('app.app_domain'), true, true, false, 'lax');
    }

    public function getAccessToken(): ?AccessToken
    {
        $accessTokenOfJwt = Cookie::get('accessToken');
        if ($accessTokenOfJwt === null) {
            return null;
        }
        return AccessToken::reconstruct($accessTokenOfJwt);
    }

    public function storeRefreshToken(RefreshToken $refreshToken, RefreshTokenExpirationDate $refreshTokenExpirationDate): void
    {
        Cookie::make('refreshToken', $refreshToken->toJwtToken(new FirebaseJwtTokenHandler()), $refreshTokenExpirationDate->value, '/', config('app.app_domain'), true, true, false, 'lax');
    }

    public function getRefreshToken(): ?RefreshToken
    {
        $refreshTokenOfJwt = Cookie::get('refreshToken');
        if ($refreshTokenOfJwt === null) {
            return null;
        }
        return RefreshToken::reconstructFromJwtToken(new FirebaseJwtTokenHandler(), $refreshTokenOfJwt);
    }
}