<?php

namespace packages\adapter\oauth\client;

use packages\domain\model\oauth\client\AClientData;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\ClientSecret;
use packages\domain\model\oauth\client\RedirectUrlList;

class ClientData extends AClientData
{
    public function __construct(
        ClientId $clientId,
        ClientSecret $clientSecret,
        RedirectUrlList $redirectUriList
    )
    {
        parent::__construct($clientId, $clientSecret, $redirectUriList);
    }

    protected function baseUrl(): string
    {
        return config('app.url');
    }
}