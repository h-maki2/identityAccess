<?php

namespace packages\adapter\session;

use Illuminate\Support\Facades\Auth;
use packages\domain\model\authenticationInformaion\SessionAuthentication;
use packages\domain\model\authenticationInformaion\UserId;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;

class LaravelSessionAuthentication implements SessionAuthentication
{
    public function markAsLoggedIn(UserId $userId): void
    {
        Auth::guard('web')->loginUsingId($userId->value);
    }

    public function getUserId(): ?UserId
    {
        if (Auth::check()) {
            return new UserId(new IdentifierFromUUIDver7, Auth::id());
        }

        return null;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
    }
}