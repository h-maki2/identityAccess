<?php

namespace packages\adapter\oauth\client;

use packages\domain\model\oauth\client\AClientData;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\ClientSecret;
use packages\domain\model\oauth\client\RedirectUrl;

class ClientData extends AClientData
{
    public function __construct(
        ClientId $clientId,
        ClientSecret $clientSecret,
        RedirectUrl $redirectUri
    )
    {
        parent::__construct($clientId, $clientSecret, $redirectUri);
    }

    protected function baseUrl(): string
    {
        return config('app.url');
    }
}