<?php

namespace packages\test\helpers\oauth;

use Laravel\Passport\ClientRepository;
use Laravel\Passport\Client as PassportClient;

class ClientTestDataCreator
{
    public static function create(
        ?string $redirectUrl = null
    ): PassportClient
    {
        $clientRepository = new ClientRepository();

        // クライアントを作成
        $client = $clientRepository->create(
            null,
            'Test Client',
            $redirectUrl ?? 'http://localhost:8080/callback'
        );

        return $client;
    }
}