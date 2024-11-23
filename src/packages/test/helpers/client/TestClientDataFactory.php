<?php

namespace packages\test\helpers\client;

use packages\domain\model\oauth\client\ClientData;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\ClientSecret;
use packages\domain\model\oauth\client\RedirectUrl;

class TestClientDataFactory
{
    public static function create(
        ?ClientId $clientId = null,
        ?ClientSecret $clientSecret = null,
        ?RedirectUrl $redirectUri = null
    ): ClientDataForTest
    {
        $clientId = $clientId ?? new ClientId('1');
        $clientSecret = $clientSecret ?? new ClientSecret('client_secret');
        $redirectUri = $redirectUri ?? new RedirectUrl('http://localhost:8080/callback');

        return new ClientDataForTest($clientId, $clientSecret, $redirectUri);
    }
}