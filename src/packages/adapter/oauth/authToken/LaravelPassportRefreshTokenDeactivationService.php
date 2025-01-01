<?php

namespace packages\adapter\oauth\authToken;

use Illuminate\Support\Facades\DB;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\IRefreshTokenDeactivationService;

class LaravelPassportRefreshTokenDeactivationService implements IRefreshTokenDeactivationService
{
    public function deactivate(AccessToken $accessToken): void
    {
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id())
            ->update(['revoked' => true]);
    }
}