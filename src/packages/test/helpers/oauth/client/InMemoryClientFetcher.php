<?php

namespace packages\test\helpers\oauth\client;

use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\ClientSecret;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\oauth\client\RedirectUrlList;

class InMemoryClientFetcher implements IClientFetcher
{
    private array $clientObjList = [];

    public function setTestClientData(ClientDataForTest $clientData): void
    {
        $this->clientObjList[$clientData->clientId()] = $clientData;
    }

    public function fetchById(ClientId $cleintId): ?ClientDataForTest
    {
        return $this->clientObjList[$cleintId->value] ?? null;
    }
}