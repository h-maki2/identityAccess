<?php

namespace packages\test\helpers\authenticationAccount;

use App\Models\User;
use packages\domain\model\authenticationAccount\UserId;

class TestAccessTokenCreator
{
    public static function create(UserId $id): string
    {
        return User::find($id->value)->createToken('Test Token')->accessToken;
    }
}