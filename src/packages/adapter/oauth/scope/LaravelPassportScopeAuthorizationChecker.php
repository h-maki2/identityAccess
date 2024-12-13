<?php

namespace packages\adapter\oauth\scope;

use App\Models\AuthenticationInformation;
use Illuminate\Support\Facades\Auth;
use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\common\exception\AuthenticationException;
use packages\domain\model\oauth\scope\IScopeAuthorizationChecker;
use packages\domain\model\oauth\scope\Scope;

class LaravelPassportScopeAuthorizationChecker implements IScopeAuthorizationChecker
{
    public function isAuthorized(UserId $userId, Scope $scope): bool
    {
        $authInfo = AuthenticationInformation::find($userId->value);
        if ($authInfo === null) {
            throw new AuthenticationException('認証情報が見つかりません');
        }
        return $authInfo->tokenCan($scope->value);
    }
}