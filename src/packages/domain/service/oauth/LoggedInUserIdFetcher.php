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
class LoggedInUserIdFetcher
{
    private AuthenticationService $authService;
    private IScopeAuthorizationChecker $scopeAuthorizationChecker;

    public function __construct(
        AuthenticationService $authService,
        IScopeAuthorizationChecker $scopeAuthorizationChecker
    )
    {
        $this->authService = $authService;
        $this->scopeAuthorizationChecker = $scopeAuthorizationChecker;
    }

    public function fetch(Scope $scope): UserId
    {
        if (!$this->scopeAuthorizationChecker->isAuthorized($scope)) {
            throw new AuthenticationException('許可されていないリクエストです。');
        }

        $userId = $this->authService->loggedInUserId();
        if ($userId === null) {
            throw new AuthenticationException('ユーザーがログインしていません');
        }

        return $userId;
    }
}