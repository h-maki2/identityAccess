<?php

namespace packages\test\helpers\oauth\authToken;

use Illuminate\Support\Facades\DB;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\RefreshToken;

class TestAuthTokenService
{
    public function isAccessTokenDeactivated(AccessToken $accessToken): bool
    {
        $token = DB::table('oauth_access_tokens')
            ->where('id', $accessToken->id())
            ->first();
        
        return $token === null;
    }

    public function isRefreshTokenDeactivated(RefreshToken $refreshToken): bool
    {
        $token = DB::table('oauth_refresh_tokens')
            ->where('id', $refreshToken->id())
            ->first();

        return $token === null;
    }
}