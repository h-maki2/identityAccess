<?php

namespace packages\domain\model\oauth\client;

use InvalidArgumentException;
use packages\domain\model\oauth\scope\ScopeList;

abstract class AClientData
{
    private ClientId $clientId;
    private ClientSecret $clientSecret;
    private RedirectUrl $redirectUrl;

    public function __construct(
        ClientId $clientId,
        ClientSecret $clientSecret,
        RedirectUrl $redirectUrl
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
    }

    public function clientId(): string
    {
        return $this->clientId->value;
    }

    public function clientSecret(): string
    {
        return $this->clientSecret->value;
    }

    public function redirectUrl(): string
    {
        return $this->redirectUrl->value;
    }

    /**
     * リダイレクトURIが入力されたリダイレクトURIと一致しているか判定する
     */
    public function hasRedirectUrlEntered(RedirectUrl $enteredRedirectUrl): bool
    {
        return $this->redirectUrl->equals($enteredRedirectUrl);
    }

    /**
     * 認可コード取得URLを作成する
     * レスポンスタイプとリダイレクトURLが正しくない場合は例外を投げる
     */
    public function urlForObtainingAuthorizationCode(
        RedirectUrl $enteredRedirectUrl,
        string $reponseType,
        string $state,
        ScopeList $scopeList
    ): string
    {
        if (!$this->hasRedirectUrlEntered($enteredRedirectUrl)) {
            throw new InvalidArgumentException('リダイレクトURIが一致しません。');
        }

        return $this->baseUrl() . '/oauth/authorize?' . $this->queryParam($reponseType, $state, $scopeList);
    }

    protected function queryParam(string $reponseType, string $state, ScopeList $scopeList): string
    {
        return http_build_query([
            'response_type' => $reponseType,
            'client_id' => $this->clientId->value,
            'redirect_uri' => $this->redirectUrl->value,
            'state' => $state,
            'scope' => $scopeList->stringValue()
        ]);
    }

    abstract protected function baseUrl(): string;
}