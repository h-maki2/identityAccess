<?php

namespace packages\domain\model\authentification\client;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;

abstract class AClientRepository implements ClientRepositoryInterface
{
    abstract protected function findById(ClientId $id): Client;

    public function getClientEntity(string $clientId): ?ClientEntityInterface
    {
        $clientId = new ClientId(new IdentifierFromUUIDver7(), $clientId);
        return $this->findbyId($clientId);
    }
}