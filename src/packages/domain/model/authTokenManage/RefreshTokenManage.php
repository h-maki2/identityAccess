<?php

namespace packages\domain\model\authTokenManage;

use packages\domain\model\userProfile\UserId;

class RefreshTokenManage
{
    readonly RefreshToken $refreshToken;
    readonly UserId $userId;

    public function __construct(RefreshToken $refreshToken, UserId $userId)
    {
        $this->refreshToken = $refreshToken;
        $this->userId = $userId;
    }
}