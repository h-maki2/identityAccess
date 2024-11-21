<?php

use packages\adapter\oauth\client\ClientData;
use packages\domain\model\oauth\client\ClientId;
use packages\domain\model\oauth\client\ClientSecret;
use packages\domain\model\oauth\client\RedirectUrl;
use packages\domain\model\oauth\client\ResponseType;
use Tests\TestCase;

class ClientDataTest extends TestCase
{
    public function test_入力されたレスポンスタイムとリダイレクトURKが正しい場合、認可コード取得用のURLを取得できる()
    {
        // given
        // クライアントデータを生成
        $clientId = new ClientId('1');
        $clientSecret = new ClientSecret('client_secret');
        $redirectUri = new RedirectUrl('http://example.com/callback');

        $clientData = new ClientData($clientId, $clientSecret, $redirectUri);

        // when
        $enterdRedirectUri = new RedirectUrl('http://example.com/callback');
        $responseType = ResponseType::Code;
        $urlForObtainingAuthorizationCode = $clientData->urlForObtainingAuthorizationCode($responseType, $enterdRedirectUri);

        // then
        $this->assertEquals('http://identity.todoapp.local/oauth/authorize?response_type=code&client_id=1&redirect_uri=http%3A%2F%2Fexample.com%2Fcallback', $urlForObtainingAuthorizationCode);
    }
}