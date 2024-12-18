<?php

namespace packages\test\helpers\client;

use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\authenticationAccount\TestAuthenticationAccountFactory;
use App\Models\authenticationAccount as EloquentAuthenticationAccount;
use packages\adapter\oauth\authToken\LaravelPassportAccessToken;
use packages\domain\model\oauth\scope\ScopeList;

class AccessTokenTestDataCreator
{
    private AuthenticationAccountTestDataCreator $authenticationAccountTestDataCreator;

    public function __construct(EloquentAuthenticationAccountRepository $authenticationAccountRepository)
    {
        $this->authenticationAccountTestDataCreator = new AuthenticationAccountTestDataCreator($authenticationAccountRepository);
    }

    /**
     * テスト用のアクセストークンを作成する
     */
    public function create(
        ?ScopeList $scopeList = null
    ): LaravelPassportAccessToken
    {
        $scopesString = $scopeList ? $scopeList->stringValue() : '';
        $authInfo = $this->authenticationAccountTestDataCreator->create();
        $eloquentAuthenticationAccount = EloquentAuthenticationAccount::find($authInfo->id()->value);
        return new LaravelPassportAccessToken($eloquentAuthenticationAccount->createToken('Test Token', [$scopesString])->accessToken);
    }
}