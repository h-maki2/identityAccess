<?php

namespace packages\test\helpers\client;

use Laravel\Passport\Client as PassportClient;

class ClientTestDataCreator
{
    public static function create(
        ?string $redirectUrl = null
    ): PassportClient
    {
        return PassportClient::create([
            'name' => 'Test Client',
            'redirect' => $redirectUrl ?? 'http://localhost:8080/callback',
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
        ]);
    }
}