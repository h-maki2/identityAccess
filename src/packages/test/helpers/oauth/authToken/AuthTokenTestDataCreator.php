<?php

namespace packages\test\helpers\oauth\authToken;

use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Grant\PasswordGrant;
use packages\domain\model\authenticationAccount\AuthenticationAccount;
use packages\domain\model\authenticationAccount\UserEmail;
use packages\domain\model\authenticationAccount\UserPassword;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\RefreshToken;
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
        ?string $emailString = null,
        ?string $passwordString = null
    ): AuthTokenTestData
    {
        $emailString = $emailString ?? 'test@example.com';
        $passwordString = $passwordString ?? 'abcABC123!';
        $authenticationAccount = TestAuthenticationAccountFactory::create(
            email: new UserEmail($emailString),
            password: UserPassword::create($passwordString)
        );
        $this->authenticationAccountTestDataCreator->create(
            $authenticationAccount->email(),
            $authenticationAccount->password(),
            $authenticationAccount->definitiveRegistrationCompletedStatus(),
            $authenticationAccount->id(),
            $authenticationAccount->loginRestriction(),
            $authenticationAccount->unsubscribeStatus()
        );

        $client = ClientTestDataCreator::createPasswordGrantClient();

        var_dump($authenticationAccount);

        // テスト用のアクセストークンとリフレッシュトークンを取得
        $response = Http::asForm()->post('http://localhost/oauth/token', [
            'grant_type' => 'password',
            'client_id' => '115',
            'client_secret' => 'J5OkkhYr4GaFSkph9XFdo2o2FUDyNEqBBZ3Gk9ZR',
            'username' => $authenticationAccount->email()->value,
            'password' => $passwordString,
            'scope' => '',
        ]);

        $tokens = $response->json();

        var_dump($tokens);

        return new AuthTokenTestData(
            new AccessToken($tokens['access_token']),
            new RefreshToken($tokens['refresh_token'])
        );
    }
}