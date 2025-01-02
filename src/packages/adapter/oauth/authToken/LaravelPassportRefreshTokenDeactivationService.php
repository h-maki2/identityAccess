<?php

namespace packages\adapter\oauth\authToken;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\RefreshToken as PassportRefreshToken;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\IRefreshTokenDeactivationService;
use packages\domain\model\oauth\authToken\RefreshToken;

class LaravelPassportRefreshTokenDeactivationService implements IRefreshTokenDeactivationService
{
    public function deactivate(AccessToken $accessToken): void
    {
        $refreshTokenModel = PassportRefreshToken::where('access_token_id', $accessToken->id())->first();

        if ($refreshTokenModel === null) {
            return;
        }

        $refreshTokenModel->delete();
    }
}