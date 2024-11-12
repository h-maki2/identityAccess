<?php

namespace packages\test\helpers\client;

use packages\domain\model\client\ClientData;

class TestClientDataFactory
{
    public static function create(
        ?string $clientId = null,
        ?string $clientSecret = null,
        ?string $redirectUri = null
    ): ClientData
    {
        $clientId = $clientId ?? '1';
        $clientSecret = $clientSecret ?? 'client_secret';
        $redirectUri = $redirectUri ?? 'http://localhost:8080/callback';

        return new ClientData($clientId, $clientSecret, $redirectUri);
    }
}