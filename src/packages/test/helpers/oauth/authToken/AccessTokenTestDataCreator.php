<?php

namespace packages\test\helpers\oauth\authToken;

use App\Models\AuthenticationInformation;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshToken as PassportRefreshToken;
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
use Illuminate\Support\Str;

class AccessTokenTestDataCreator
{
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;

    public function __construct(
        AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator
    ) {
        $this->authenticationAccountTestDataCreator = $authenticationAccountTestDataCreator;
    }

    public function create(
        ?AuthenticationAccount $authenticationAccount = null,
    ): AccessToken
    {
        // 認証アカウントを作成
        $authenticationAccount = $authenticationAccount ?? TestAuthenticationAccountFactory::create();
        $this->authenticationAccountTestDataCreator->create(
            $authenticationAccount->email(),
            $authenticationAccount->password(),
            $authenticationAccount->definitiveRegistrationCompletedStatus(),
            $authenticationAccount->id(),
            $authenticationAccount->loginRestriction(),
            $authenticationAccount->unsubscribeStatus()
        );

        // アクセストークンを作成
        $authInfo = AuthenticationInformation::where('user_id', $authenticationAccount->id()->value)->first();
        $accessToken = new AccessToken($authInfo->createToken('Test Access Token')->accessToken);
        
        // リフレッシュトークンも作成しておく
        $refreshTokenString = PassportRefreshToken::create([
            'id' => 'test_refresh_token_id',
            'access_token_id' => $accessToken->id(),
            'revoked' => false,
        ]);
        
        return $accessToken;
    }
}