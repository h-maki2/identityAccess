<?php

namespace packages\domain\service\oauth;

use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\oauth\client\RedirectUrl;
use RuntimeException;

class ClientService
{
    private IClientFetcher $clientFetcher;

    public function __construct(IClientFetcher $clientFetcher)
    {
        $this->clientFetcher = $clientFetcher;
    }

    public function isCorrectRedirectUrl(ClientId $clientId, RedirectUrl $redirectUrl): bool
    {
        $clientData = $this->clientFetcher->fetchById($clientId);
        if ($clientData === null) {
            throw new RuntimeException('クライアントが見つかりません');
        }

        return $clientData->hasEntereRedirectUrl($redirectUrl);
    }
}