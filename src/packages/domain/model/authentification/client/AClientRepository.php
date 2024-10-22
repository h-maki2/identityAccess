<?php

namespace packages\domain\model\authentification\client;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;

abstract class AClientRepository implements ClientRepositoryInterface
{
    abstract protected function findById(ClientId $id): ?Client;

    public function getClientEntity(string $clientId): ?ClientEntityInterface
    {
        $clientId = new ClientId(new IdentifierFromUUIDver7(), $clientId);
        return $this->findbyId($clientId);
    }

    public function validateClient(string $clientId, ?string $clientSecret, ?string $grantType): bool
    {
        $clientId = new ClientId(new IdentifierFromUUIDver7(), $clientId);
        $client = $this->findById($clientId);

        if ($client === null) {
            return false;
        }

        if ($client->isConfidential()) {
            return $client->secret()->equals($clientSecret);
        }

        // 必要に応じて、グラントタイプのチェックもここで実装可能
        // if ($grantType && !in_array($grantType, explode(',', $client['allowed_grant_types']))) {
        //     return false; // グラントタイプが不正な場合
        // }

        return true;
    }
}