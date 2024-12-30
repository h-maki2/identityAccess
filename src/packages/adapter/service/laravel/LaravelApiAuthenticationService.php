<?php

namespace packages\adapter\service\laravel;

use Illuminate\Support\Facades\Auth;
use packages\domain\model\authenticationAccount\UserId;
use packages\domain\service\authenticationAccount\AuthenticationService;

class LaravelApiAuthenticationService implements AuthenticationService
{
    public function markAsLoggedIn(UserId $userId): void
    {
        Auth::guard('api')->loginUsingId($userId->value);
    }

    public function loggedInUserId(): ?UserId
    {
        if (Auth::check()) {
            return new UserId(Auth::id());
        }

        return null;
    }

    public function logout(): void
    {
        Auth::guard('api')->logout();
    }
}