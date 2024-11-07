<?php

namespace packages\domain\model\client;

interface IClientFetcher
{
    public function fetchById(string $clientId): ?ClientData;
}