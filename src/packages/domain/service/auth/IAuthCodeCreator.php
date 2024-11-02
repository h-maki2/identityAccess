<?php

use packages\domain\model\userProfile\UserId;

interface IAuthCodeCreator
{
    /**
     * @param Scopes[] $scopes
     */
    public function create(UserId $id, array $scopes);
}