<?php

namespace packages\domain\model\oauth\scope;

use packages\domain\model\authenticationAccount\UserId;

interface IScopeAuthorizationChecker
{
    /**
     * 指定したスコープが許可されているかどうかを判定
     */
    public function isAuthorized(Scope $scope): bool;
}