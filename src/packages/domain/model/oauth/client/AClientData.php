<?php

namespace packages\domain\model\oauth\client;

use InvalidArgumentException;
use packages\domain\model\oauth\scope\ScopeList;

abstract class AClientData
{
    private ClientId $clientId;
    private ClientSecret $clientSecret;
    private RedirectUrlList $redirectUrlList;

    public function __construct(
        ClientId $clientId,
        ClientSecret $clientSecret,
        RedirectUrlList $redirectUrlList
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrlList = $redirectUrlList;
    }

    public function clientId(): string
    {
        return $this->clientId->value;
    }

    public function clientSecret(): string
    {
        return $this->clientSecret->value;
    }

    /**
     * リダイレクトURIが入力されたリダイレクトURIと一致しているか判定する
     */
    public function hasEntereRedirectUrl(RedirectUrl $enteredRedirectUrl): bool
    {
        return $this->redirectUrlList->contains($enteredRedirectUrl);
    }

    /**
     * 認可コード取得URLを作成する
     */
    public function urlForObtainingAuthorizationCode(
        RedirectUrl $enteredRedirectUrl,
        string $reponseType,
        string $state,
        ScopeList $scopeList
    ): string
    {
        if (!$this->hasEntereRedirectUrl($enteredRedirectUrl)) {
            throw new InvalidArgumentException('リダイレクトURIが一致しません。');
        }

        return $this->baseUrl() . '/oauth/authorize?' . $this->queryParam($reponseType, $state, $scopeList, $enteredRedirectUrl);
    }

    protected function queryParam(
        string $reponseType, 
        string $state, 
        ScopeList $scopeList, 
        RedirectUrl $enteredRedirectUrl): string
    {
        return http_build_query([
            'response_type' => $reponseType,
            'client_id' => $this->clientId->value,
            'redirect_uri' => $enteredRedirectUrl->value,
            'state' => $state,
            'scope' => $scopeList->stringValue()
        ]);
    }

    abstract protected function baseUrl(): string;
}