<?php

namespace packages\application\authentication\displayLoginForm;

use packages\domain\model\oauth\client\AClientData;
use packages\domain\model\oauth\client\State;
use packages\domain\model\oauth\scope\ScopeList;

class DisplayLoginFormResult
{
    readonly string $clientId;
    readonly string $redirectUrl;
    readonly string $responseType;
    readonly string $state;
    readonly string $scopes;

    public function __construct(
        AClientData $clientData,
        string $responseType,
        State $state,
        ScopeList $scopeList
    )
    {
        $this->clientId = $clientData->clientId();
        $this->redirectUrl = $clientData->redirectUrl();
        $this->responseType = $responseType;
        $this->state = $state->value;
        $this->scopes = $scopeList->stringValue();
    }
}