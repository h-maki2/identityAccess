<?php

namespace packages\domain\service\oauth;

use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\common\exception\AuthenticationException;
use packages\domain\model\oauth\scope\IScopeAuthorizationChecker;
use packages\domain\model\oauth\scope\Scope;
use packages\domain\service\authenticationAccount\AuthenticationService;

/**
 * ログイン済みのユーザーIDを取得する
 */
interface ILoggedInUserIdFetcher
{
    public function fetch(Scope $scope): UserId;
}