<?php

namespace packages\adapter\oauth\scope;

use Illuminate\Http\Request;
use packages\domain\model\oauth\scope\IScopeAuthorizationChecker;
use packages\domain\model\oauth\scope\Scope;

class LaravelPassportScopeAuthorizationChecker implements IScopeAuthorizationChecker
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function isAuthorized(Scope $scope): bool
    {
        return $this->request->user()->tokenCan($scope->value);
    }
}