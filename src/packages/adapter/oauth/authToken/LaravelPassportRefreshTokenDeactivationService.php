<?php

namespace packages\adapter\oauth\authToken;

use Illuminate\Support\Facades\DB;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\IRefreshTokenDeactivationService;
use packages\domain\model\oauth\authToken\RefreshToken;

class LaravelPassportRefreshTokenDeactivationService implements IRefreshTokenDeactivationService
{
    public function deactivate(RefreshToken $refreshToken): void
    {
        $refreshToken = DB::table('oauth_refresh_tokens')
            ->where('id', $refreshToken->id())
            ->first();
        
        $refreshToken->delete();
    }
}