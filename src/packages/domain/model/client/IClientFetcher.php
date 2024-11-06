<?php

namespace packages\domain\model\client;

interface IClientFetcher
{
    public function findById(string $clientId): ?ClientData;
}