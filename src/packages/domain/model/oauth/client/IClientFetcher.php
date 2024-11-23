<?php

namespace packages\domain\model\oauth\client;

interface IClientFetcher
{
    public function fetchById(ClientId $clientId): ?AClientData;
}