<?php

namespace packages\domain\model\oauth\client;

use InvalidArgumentException;

abstract class AClientData
{
    private ClientId $clientId;
    private ClientSecret $clientSecret;
    private RedirectUrl $redirectUri;
    // private array $scope;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    public function clientId(): string
    {
        return $this->clientId->value;
    }

    public function clientSecret(): string
    {
        return $this->clientSecret->value;
    }

    public function redirectUri(): string
    {
        return $this->redirectUri->value;
    }

    /**
     * 認可コード取得URLを作成する
     * レスポンスタイプとリダイレクトURLが正しくない場合は例外を投げる
     */
    public function urlForObtainingAuthorizationCode(
        ResponseType $reponseType,
        RedirectUrl $enteredRedirectUri
    ): string
    {
        if (!$reponseType->isCode()) {
            throw new InvalidArgumentException('無効なレスポンスタイプです。');
        }

        if (!$this->hasRedirectUriEntered($enteredRedirectUri)) {
            throw new InvalidArgumentException('リダイレクトURIが一致しません。');
        }

        return $this->baseUrl() . '/oauth/authorize?' . $this->queryParam($reponseType);
    }

    protected function queryParam(ResponseType $reponseType): string
    {
        return http_build_query([
            'response_type' => $reponseType->value,
            'client_id' => $this->clientId->value,
            'redirect_uri' => $this->redirectUri->value
        ]);
    }

    /**
     * クライアントシークレットが入力されたクライアントシークレットと一致しているか判定する
     */
    protected function hasClientSecretEntered(ClientSecret $enterdClientSecret): bool
    {
        return $this->clientSecret->equals($enterdClientSecret);
    }

    /**
     * リダイレクトURIが入力されたリダイレクトURIと一致しているか判定する
     */
    protected function hasRedirectUriEntered(RedirectUrl $enteredRedirectUri): bool
    {
        return $this->redirectUri->equals($enteredRedirectUri);
    }

    abstract protected function baseUrl(): string;
}