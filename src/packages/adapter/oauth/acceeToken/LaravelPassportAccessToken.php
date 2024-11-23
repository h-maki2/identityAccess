<?php

namespace packages\adapter\oauth\accessToken;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use packages\domain\model\oauth\accessToken\AccessToken;

class LaravelPassportAccessToken extends AccessToken
{
    public function id(): string
    {
        $publicKey = file_get_contents(storage_path('oauth-public.key'));
        $decoded = JWT::decode($this->value, new Key($publicKey, 'RS256'));
        return $decoded->jti;
    }
}