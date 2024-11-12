<?php

namespace packages\domain\model\client;

abstract class ClientData
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
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
        return $this->clientId;
    }

    public function clientSecret(): string
    {
        return $this->clientSecret;
    }

    public function redirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * 認可コード取得URLを作成する
     */
    abstract public function urlForObtainingAuthorizationCode(): string;

    protected function urlPathForObtainingAuthorizationCode(): string
    {
        return '/oauth/authorize?' . $this->queryParam();
    }

    protected function queryParam(): string
    {
        return http_build_query([
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri
        ]);
    }
}