<?php

namespace packages\test\helpers\authenticationInformation;

use packages\domain\model\authenticationInformation\UserId;
use packages\domain\model\common\identifier\IdentifierFromUUIDver7;

class TestUserIdFactory
{
    public static function createUserId(): UserId
    {
        return new UserId('0188b2a6-bd94-7ccf-9666-1df7e26ac6b8');
    }
}