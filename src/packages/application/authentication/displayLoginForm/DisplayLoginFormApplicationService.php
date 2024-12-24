<?php

namespace packages\application\authentication\displayLoginForm;

use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\IClientFetcher;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\domain\model\oauth\client\State;
use packages\domain\model\oauth\scope\ScopeList;
use UnexpectedValueException;

class DisplayLoginFormApplicationService
{
    private IClientFetcher $clientFetcher;

    public function __construct(IClientFetcher $clientFetcher)
    {
        $this->clientFetcher = $clientFetcher;
    }

    public function handle(
        string $clientIdString,
        string $redirectUrlString,
        string $responseType,
        string $stateString,
        string $scopes
    ): DisplayLoginFormResult
    {
        $clientId = new ClientId($clientIdString);

        $clientData = $this->clientFetcher->fetchById($clientId);
        if ($clientData === null) {
            throw new UnexpectedValueException('クライアントが見つかりません。');
        }
        $redirectUrl = new RedirectUrl($redirectUrlString);
        if (!$clientData->hasRedirectUrlEntered($redirectUrl)) {
            throw new UnexpectedValueException('リダイレクトURIが一致しません。');
        }

        $state = new State($stateString);
        $scopeList = ScopeList::createFromString($scopes);

        return new DisplayLoginFormResult(
            $clientData, 
            $responseType, 
            $state, 
            $scopeList
        );
    }
}