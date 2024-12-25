<?php

namespace packages\adapter\service\laravel;

use Illuminate\Support\Facades\Cookie;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\IAccessTokenCookieService;

class LaravelAccessTokenCookieService implements IAccessTokenCookieService
{
    public function fetch(): ?AccessToken
    {
       // クッキーが存在しない場合のデフォルト値を指定
        $accessTokenString = Cookie::get('accessToken', null);

        if ($accessTokenString === null) {
            return null;
        }

        return new AccessToken($accessTokenString);
    }
}