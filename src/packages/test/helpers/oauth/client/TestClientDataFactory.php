<?php

namespace packages\test\helpers\oauth\client;

use packages\domain\model\oauth\client\ClientData;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\ClientSecret;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\domain\model\oauth\client\RedirectUrlList;

class TestClientDataFactory
{
    public static function create(
        ?ClientId $clientId = null,
        ?ClientSecret $clientSecret = null,
        ?RedirectUrlList $redirectUriList = null
    ): ClientDataForTest
    {
        $clientId = $clientId ?? new ClientId('1');
        $clientSecret = $clientSecret ?? new ClientSecret('client_secret');
        $redirectUriList = $redirectUriList ?? new RedirectUrlList('http://localhost:8080/callback');

        return new ClientDataForTest($clientId, $clientSecret, $redirectUriList);
    }
}