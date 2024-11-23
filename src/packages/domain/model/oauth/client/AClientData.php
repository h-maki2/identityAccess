<?php

namespace packages\domain\model\oauth\client;

use InvalidArgumentException;

abstract class AClientData
{
    private ClientId $clientId;
    private ClientSecret $clientSecret;
    private RedirectUrl $redirectUrl;
    // private array $scope;

    protected function __construct(
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
     * 認可コード取得URLを作成する
     * レスポンスタイプとリダイレクトURLが正しくない場合は例外を投げる
     */
    public function urlForObtainingAuthorizationCode(
        RedirectUrl $enteredRedirectUrl,
        string $reponseType
    ): string
    {
        if (!$this->hasRedirectUrlEntered($enteredRedirectUrl)) {
            throw new InvalidArgumentException('リダイレクトURIが一致しません。');
        }

        return $this->baseUrl() . '/oauth/authorize?' . $this->queryParam($reponseType);
    }

    protected function queryParam(string $reponseType): string
    {
        return http_build_query([
            'response_type' => $reponseType,
            'client_id' => $this->clientId->value,
            'redirect_uri' => $this->redirectUrl->value
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
    protected function hasRedirectUrlEntered(RedirectUrl $enteredRedirectUrl): bool
    {
        return $this->redirectUrl->equals($enteredRedirectUrl);
    }

    abstract protected function baseUrl(): string;
}