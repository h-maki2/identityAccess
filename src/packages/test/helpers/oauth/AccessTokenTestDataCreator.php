<?php

namespace packages\test\helpers\oauth;

use packages\adapter\persistence\eloquent\EloquentAuthenticationAccountRepository;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\test\helpers\authenticationAccount\AuthenticationAccountTestDataCreator;
use packages\test\helpers\authenticationAccount\TestAuthenticationAccountFactory;
use App\Models\User as EloquentUser;
use packages\domain\model\authenticationAccount\AuthenticationAccount;
use packages\domain\model\oauth\scope\ScopeList;

class AccessTokenTestDataCreator
{
    /**
     * テスト用のアクセストークンを作成する
     */
    public function create(
        ?ScopeList $scopeList = null,
        ?AuthenticationAccount $authAccount = null
    ): AccessToken
    {
        $scopesString = $scopeList ? $scopeList->stringValue() : '';
        $authAccount = $authAccount ?? TestAuthenticationAccountFactory::create();
        $eloquentUser = EloquentUser::find($authAccount->id()->value);
        return new AccessToken($eloquentUser->createToken('Test Token', $this->scopeList($scopesString))->accessToken);
    }

    private function scopeList(string $scopeString): array
    {
        return explode(' ', $scopeString);
    }
}