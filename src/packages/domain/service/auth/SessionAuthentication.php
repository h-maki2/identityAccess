<?php

namespace packages\domain\service\auth;

use packages\domain\model\userProfile\UserId;

abstract class SessionAuthentication
{
    abstract public function setUserId(UserId $userId): void;

    abstract public function getUserId(): UserId;
}