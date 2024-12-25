<?php

namespace packages\adapter\oauth\client;

use App\Models\Client;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\IClientFetcher;
use App\Models\Client as EloquentClient;
use packages\domain\model\oauth\client\ClientSecret;
use packages\domain\model\oauth\client\RedirectUrlList;

class LaravelPassportClientFetcher implements IClientFetcher
{
    public function fetchById(ClientId $clientId): ?ClientData
    {
        $client = Client::where('id', $clientId->value)->first();
        if ($client === null) {
            return null;
        }

        return $this->toClientData($client);
    }

    private function toClientData(EloquentClient $client): ClientData
    {
        return new ClientData(
            new ClientId($client->id),
            new ClientSecret($client->secret),
            new RedirectUrlList($client->redirect)
        );
    }
}