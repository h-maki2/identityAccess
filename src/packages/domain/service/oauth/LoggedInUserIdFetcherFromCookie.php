<?php

namespace packages\domain\service\oauth;

use packages\domain\model\authenticationAccount\UserId;
use packages\domain\model\common\exception\AuthenticationException;
use packages\domain\model\oauth\authToken\IAccessTokenCookieService;
use packages\domain\model\oauth\scope\IScopeAuthorizationChecker;
use packages\domain\model\oauth\scope\Scope;

class LoggedInUserIdFetcherFromCookie implements ILoggedInUserIdFetcher
{
    private IAccessTokenCookieService $accessTokenCookieService;
    private IScopeAuthorizationChecker $scopeAuthorizationChecker;

    public function __construct(
        IAccessTokenCookieService $accessTokenCookieService,
        IScopeAuthorizationChecker $scopeAuthorizationChecker
    )
    {
        $this->accessTokenCookieService = $accessTokenCookieService;
        $this->scopeAuthorizationChecker = $scopeAuthorizationChecker;
    }

    public function fetch(Scope $scope): UserId
    {
        if (!$this->scopeAuthorizationChecker->isAuthorized($scope)) {
            throw new AuthenticationException('許可されていないリクエストです。');
        }

        $accessToken = $this->accessTokenCookieService->fetch();

        return $accessToken->userId();
    }
}