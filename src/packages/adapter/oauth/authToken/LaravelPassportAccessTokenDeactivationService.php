<?php

namespace packages\adapter\oauth\authToken;

use Illuminate\Support\Facades\DB;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\IAccessTokenDeactivationService;

class LaravelPassportAccessTokenDeactivationService implements IAccessTokenDeactivationService
{
    public function deactivate(AccessToken $accessToken): void
    {
        DB::table('oauth_access_tokens')
            ->where('id', $accessToken->id())
            ->update(['revoked' => true]);
    }
}