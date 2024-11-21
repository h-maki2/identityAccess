<?php

namespace packages\test\helpers\client;

use packages\domain\model\oauth\client\ClientData;

class TestClientDataFactory
{
    public static function create(
        ?string $clientId = null,
        ?string $clientSecret = null,
        ?string $redirectUri = null
    ): ClientDataForTest
    {
        $clientId = $clientId ?? '1';
        $clientSecret = $clientSecret ?? 'client_secret';
        $redirectUri = $redirectUri ?? 'http://localhost:8080/callback';

        return new ClientDataForTest($clientId, $clientSecret, $redirectUri);
    }
}