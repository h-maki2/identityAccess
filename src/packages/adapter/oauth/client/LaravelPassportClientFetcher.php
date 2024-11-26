<?php

namespace packages\adapter\oauth\client;

use App\Models\Client;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\IClientFetcher;

class LaravelPassportClientFetcher implements IClientFetcher
{
    public function fetchById(ClientId $clientId): ?ClientData
    {
        return Client::where('id', $clientId->value)->first();
    }
}