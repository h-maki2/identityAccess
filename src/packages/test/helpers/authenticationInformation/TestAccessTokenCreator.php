<?php

namespace packages\test\helpers\authenticationInformation;

use App\Models\AuthenticationInformation;
use packages\domain\model\authenticationInformation\UserId;

class TestAccessTokenCreator
{
    public static function create(UserId $id): string
    {
        return AuthenticationInformation::find($id->value)->createToken('Test Token')->accessToken;
    }
}