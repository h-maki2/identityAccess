<?php

namespace packages\domain\model\oauth\scope;

use packages\domain\model\authenticationInformation\UserId;

interface IScopeAuthorizationChecker
{
    /**
     * 指定したスコープが許可されているかどうかを判定
     */
    public function isAuthorized(UserId $userId, ScopeList $scopeList): bool;
}