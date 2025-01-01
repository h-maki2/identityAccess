<?php

namespace packages\domain\model\oauth\authToken;

use Illuminate\Support\Facades\Http;
use packages\domain\model\authenticationAccount\AuthenticationAccount;
use packages\domain\model\authenticationAccount\IAuthenticationAccountRepository;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\authenticationAccount\TestAuthenticationAccountFactory;
use packages\test\helpers\oauth\authToken\AuthTokenTestData;
use packages\test\helpers\oauth\client\ClientTestDataCreator;

class AuthTokenTestDataCreator
{
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;

    public function __construct(
        AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator
    ) {
        $this->authenticationAccountTestDataCreator = $authenticationAccountTestDataCreator;
    }

    public function create(
        ?AuthenticationAccount $authenticationAccount = null,
    ): AuthTokenTestData
    {
        $authenticationAccount = $authenticationAccount ?? TestAuthenticationAccountFactory::create();
        $this->authenticationAccountTestDataCreator->create(
            $authenticationAccount->email(),
            $authenticationAccount->password(),
            $authenticationAccount->definitiveRegistrationCompletedStatus(),
            $authenticationAccount->id(),
            $authenticationAccount->loginRestriction(),
            $authenticationAccount->unsubscribeStatus()
        );

        $client = ClientTestDataCreator::create();

        // テスト用のアクセストークンとリフレッシュトークンを取得
        $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $authenticationAccount->email()->value,
            'password' => $authenticationAccount->password()->hashedValue,
            'scope' => '',
        ]);

        return new AuthTokenTestData(
            new AccessToken($response['access_token']),
            new RefreshToken($response['refresh_token'])
        );
    }
}