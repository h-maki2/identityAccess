<?php

namespace packages\test\helpers\authenticationAccount;

use App\Models\authenticationAccount;
use packages\domain\model\authenticationAccount\UserId;

class TestAccessTokenCreator
{
    public static function create(UserId $id): string
    {
        return AuthenticationAccount::find($id->value)->createToken('Test Token')->accessToken;
    }
}