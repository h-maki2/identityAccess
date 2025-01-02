<?php

namespace packages\adapter\oauth\authToken;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\Token;
use packages\domain\model\oauth\authToken\AccessToken;
use packages\domain\model\oauth\authToken\IAccessTokenDeactivationService;

class LaravelPassportAccessTokenDeactivationService implements IAccessTokenDeactivationService
{
    public function deactivate(AccessToken $accessToken): void
    {
        $token = Token::find($accessToken->id());

        if ($token === null) {
            return;
        }
    
        $token->delete();
    }
}