<?php

namespace packages\test\helpers\oauth\client;

use Illuminate\Support\Facades\DB;
use packages\domain\model\oauth\authToken\AccessToken;

class TestAuthTokenService
{
    public function isAccessTokenDeactivated(AccessToken $accessToken): bool
    {
        $token = DB::table('oauth_access_tokens')
            ->where('id', $accessToken->id())
            ->first();
        
        return $token->revoked;
    }

    public function isRefreshTokenDeactivated(AccessToken $accessToken): bool
    {
        $token = DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id())
            ->first();

        return $token->revoked;
    }
}