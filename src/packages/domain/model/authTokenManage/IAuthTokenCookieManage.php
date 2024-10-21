<?php

namespace packages\domain\model\authTokenManage;

interface IAuthTokenCookieManage
{
    public function storeAccessToken(AccessToken $accessToken, AccessTokenExpirationDate $accessTokenExpirationDate): void;

    public function getAccessToken(): ?AccessToken;

    public function storeRefreshToken(RefreshToken $refreshToken, RefreshTokenExpirationDate $refreshTokenExpirationDate): void;

    public function getRefreshToken(): ?RefreshToken;
}